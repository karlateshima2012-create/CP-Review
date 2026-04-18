const CACHE_NAME = 'cp-review-cache-v1';
const ASSETS = [
    '/pwa/offline-queue.js',
    '/pwa/photo-upload.js'
];

self.addEventListener('install', event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS))
    );
});

self.addEventListener('activate', event => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('fetch', event => {
    // Only cache GET requests
    if (event.request.method !== 'GET') return;

    // Cache bot-scripts API? No, the frontend does it via Redis or we can just let it fetch normally.
    // For now we just implement simple network-first for GET requests to serve PWA shell
    event.respondWith(
        fetch(event.request).catch(() => caches.match(event.request))
    );
});

// Background Sync
self.addEventListener('sync', event => {
    if (event.tag === 'photo-upload-queue') {
        event.waitUntil(processPhotoQueue());
    }
});

async function processPhotoQueue() {
    // Dynamically load OfflineQueue if needed (it might not be in scope in SW normally, so we import it)
    // Actually, IndexedDB is natively available in the SW. 
    // We can open the DB and process the items.
    
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('cp_review_queue', 1);
        req.onerror = reject;
        req.onsuccess = async (e) => {
            const db = e.target.result;
            const tx = db.transaction('pending_uploads', 'readwrite');
            const store = tx.objectStore('pending_uploads');
            const idx = store.index('status');
            
            const getReq = idx.getAll(IDBKeyRange.only('pending'));
            getReq.onsuccess = async () => {
                const pendings = getReq.result;
                if (!pendings || pendings.length === 0) return resolve();

                for (let item of pendings) {
                    try {
                        const fd = new FormData();
                        fd.append('photo', item.photoBlob, `photo_${item.reviewToken}.jpg`);
                        fd.append('review_token', item.reviewToken);
                        fd.append('slug', item.slug);

                        const res = await fetch('/api/media/upload', {
                            method: 'POST', body: fd
                        });

                        if (res.ok) {
                            // Marca como done
                            item.status = 'done';
                        } else {
                            item.attempts += 1;
                            if(item.attempts > 3) item.status = 'failed';
                        }
                    } catch (err) {
                        item.attempts += 1;
                        if(item.attempts > 3) item.status = 'failed';
                    }

                    // Grava o status atualizado de volta no IDB
                    const putTx = db.transaction('pending_uploads', 'readwrite');
                    putTx.objectStore('pending_uploads').put(item);
                }
                resolve();
            };
        };
    });
}
