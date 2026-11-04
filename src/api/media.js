import api from "./axios";

/** Ambil metadata video dari URL tanpa mengunduh. */
export const getMediaInfo = (url) =>
  api.get("/api/v1/media/info", { params: { url } });

/** Upload video lokal (multipart). onProgress menerima 0-100. */
export const uploadVideo = (file, onProgress) => {
  const form = new FormData();
  form.append("file", file);
  return api.post("/api/v1/media/upload", form, {
    headers: { "Content-Type": "multipart/form-data" },
    timeout: 0,
    onUploadProgress: (event) => {
      if (event.total) {
        onProgress?.(Math.round((event.loaded / event.total) * 100));
      }
    },
  });
};

/** Daftar video yang sudah ada di storage (clip, upload, export, preview). */
export const listLibrary = (limit = 60) =>
  api.get("/api/v1/media/library", { params: { limit } });

/** Daftar aset stiker/frame yang sudah pernah di-generate (kind: sticker|frame). */
export const listAssets = (kind, limit = 120) =>
  api.get("/api/v1/media/assets", { params: { kind, limit } });

/** Buat preview potongan URL (kualitas rendah) sebelum dipotong. */
export const startPreview = (payload) =>
  api.post("/api/v1/clipping/preview", payload);

/** Potong video lokal (hasil upload) tanpa mengunduh ulang. */
export const trimLocal = (payload) =>
  api.post("/api/v1/clipping/trim", payload);
