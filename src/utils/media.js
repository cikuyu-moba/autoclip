const API_BASE = import.meta.env.VITE_API_BASE_URL || "";

/**
 * Ubah path statis backend (/storage/...) menjadi URL yang bisa dibuka browser.
 * Saat dev, Vite mem-proxy /storage ke backend sehingga cukup path relatif.
 */
export const mediaUrl = (url) => {
  if (!url) return "";
  if (/^https?:\/\//i.test(url)) return url;
  return `${API_BASE}${url.startsWith("/") ? "" : "/"}${url}`;
};
