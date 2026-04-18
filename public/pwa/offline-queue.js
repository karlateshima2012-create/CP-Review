/**
 * OfflineQueue — fila persistente de uploads em IndexedDB.
 *
 * Por que IndexedDB e não localStorage?
 *  - localStorage é síncrono e limitado a 5MB
 *  - IndexedDB é assíncrono e suporta blobs (arquivos binários)
 *  - Fotos podem ter até 5MB — localStorage quebraria
 */

const OfflineQueue = {
  DB_NAME: 'cp_review_queue',
  DB_VERSION: 1,
  STORE: 'pending_uploads',
  _db: null,

  /** Abre (ou cria) o banco IndexedDB */
  async open() {
    if (this._db) return this._db;

    return new Promise((resolve, reject) => {
      const req = indexedDB.open(this.DB_NAME, this.DB_VERSION);

      req.onupgradeneeded = (e) => {
        const db = e.target.result;
        if (!db.objectStoreNames.contains(this.STORE)) {
          const store = db.createObjectStore(this.STORE, { keyPath: 'id', autoIncrement: true });
          store.createIndex('status', 'status', { unique: false });
        }
      };

      req.onsuccess  = (e) => { this._db = e.target.result; resolve(this._db); };
      req.onerror    = () => reject(req.error);
    });
  },

  /** Adiciona item à fila. Retorna o ID gerado. */
  async enqueue({ reviewToken, slug, photoBlob, photoName }) {
    const db = await this.open();
    return new Promise((resolve, reject) => {
      const tx  = db.transaction(this.STORE, 'readwrite');
      const req = tx.objectStore(this.STORE).add({
        reviewToken, slug, photoBlob, photoName,
        status:    'pending',  // pending | uploading | done | failed
        attempts:  0,
        createdAt: new Date().toISOString(),
      });
      req.onsuccess = () => resolve(req.result);
      req.onerror   = () => reject(req.error);
    });
  },

  /** Atualiza o status de um item na fila */
  async updateStatus(id, status, extras = {}) {
    const db = await this.open();
    return new Promise((resolve, reject) => {
      const tx    = db.transaction(this.STORE, 'readwrite');
      const store = tx.objectStore(this.STORE);
      const getReq = store.get(id);
      getReq.onsuccess = () => {
        const item = { ...getReq.result, status, ...extras };
        const putReq = store.put(item);
        putReq.onsuccess = () => resolve(item);
        putReq.onerror   = () => reject(putReq.error);
      };
    });
  },

  /** Busca todos os itens pendentes (para retry automático) */
  async getPending() {
    const db = await this.open();
    return new Promise((resolve) => {
      const tx    = db.transaction(this.STORE, 'readonly');
      const index = tx.objectStore(this.STORE).index('status');
      const req   = index.getAll(IDBKeyRange.only('pending'));
      req.onsuccess = () => resolve(req.result);
    });
  },
};
