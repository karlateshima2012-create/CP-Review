/**
 * PhotoUploader — compressão e upload via multipart/form-data
 */
const PhotoUploader = {
  MAX_WIDTH: 1200,

  /**
   * 1. Redimensiona/Comprime a imagem no Client
   */
  async compress(file) {
    if (!file.type.match(/image\/(jpeg|png)/)) {
      throw new Error('Apenas JPEG/PNG são permitidos.');
    }

    const img = document.createElement('img');
    const url = URL.createObjectURL(file);
    img.src = url;

    await new Promise(r => img.onload = r);

    let width  = img.width;
    let height = img.height;

    // Reduz dimensões se passar do limite
    if (width > this.MAX_WIDTH) {
      height = Math.round(height * (this.MAX_WIDTH / width));
      width  = this.MAX_WIDTH;
    }

    const canvas = document.createElement('canvas');
    canvas.width  = width;
    canvas.height = height;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(img, 0, 0, width, height);

    URL.revokeObjectURL(url);

    // Retorna Buffer binário (Blob) comprimido (80% qualidade)
    return new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.8));
  },

  /**
   * 2. Tenta fazer o upload via fetch()
   *    Se cair a rede, coloca na fila IndexedDB.
   */
  async upload({ file, reviewToken, slug, onProgress, csrfToken }) {
    try {
      const compressed = await this.compress(file);

      // Se não há rede, entra no fluxo Offline
      if (!navigator.onLine) {
        await OfflineQueue.enqueue({ reviewToken, slug, photoBlob: compressed });
        // Registra Background Sync para subir assim que a rede voltar
        if ('serviceWorker' in navigator && 'SyncManager' in window) {
          const reg = await navigator.serviceWorker.ready;
          await reg.sync.register('photo-upload-queue');
        }
        return { status: 'queued_offline' };
      }

      // Fluxo Online Normal
      const fd = new FormData();
      fd.append('photo', compressed, `photo_${reviewToken}.jpg`);
      fd.append('review_token', reviewToken);
      fd.append('slug', slug);

      // Fetch não suporta progresso nativamente de forma simples para uploads,
      // usaria XMLHttpRequest se `onProgress` fosse crucial, mas para o SaaS usaremos fetch com AbortController
      const ctrl  = new AbortController();
      const timer = setTimeout(() => ctrl.abort(), 15000); // 15 seg timeout

      const res = await fetch('/api/media/upload', {
        method: 'POST', body: fd, signal: ctrl.signal,
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
      });

      clearTimeout(timer);

      if (!res.ok) {
        // Se deu erro 500 ou 413, falhamos. Se for rede, o CATCH pega
        throw new Error('Upload server error');
      }

      return { status: 'success', data: await res.json() };

    } catch (e) {
      console.warn("Upload falhou, direcionando para fila offline...", e);
      // Se deu timeout ou caiu a rede no meio (TypeError: Failed to fetch)
      // colocamos na fila
      try {
        const compressed = await this.compress(file);
        await OfflineQueue.enqueue({ reviewToken, slug, photoBlob: compressed });
      } catch (err) {
        // file unreadable
      }
      return { status: 'queued_error' };
    }
  }
};
