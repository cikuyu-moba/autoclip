import axios from "axios";

// Gunakan URL relatif agar request selalu memakai origin yang sama:
// - Development : Vite proxy (/api, /storage) -> backend lokal
// - Production  : nginx mem-proxy /api & /storage -> Laravel (PHP-FPM)
const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "",
  headers: {
    "Content-Type": "application/json",
  },
});

export default api;
