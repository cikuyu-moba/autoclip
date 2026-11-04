/* Format detik ke HH:MM:SS */
export const formatTime = (seconds) => {
  if (!seconds && seconds !== 0) return "00:00:00";
  const h = Math.floor(seconds / 3600);
  const m = Math.floor((seconds % 3600) / 60);
  const s = Math.floor(seconds % 60);
  return [h, m, s].map((v) => String(v).padStart(2, "0")).join(":");
};

/* Format detik ke MM:SS atau HH:MM:SS */
export const formatShort = (seconds) => {
  if (!seconds && seconds !== 0) return "0:00";
  const m = Math.floor(seconds / 60);
  const s = Math.floor(seconds % 60);
  const h = Math.floor(seconds / 3600);
  if (h > 0) return `${h}:${String(m).padStart(2, "0")}:${String(s).padStart(2, "0")}`;
  return `${m}:${String(s).padStart(2, "0")}`;
};

/* Ubah HH:MM:SS atau detik string menjadi detik float */
export const toSeconds = (value) => {
  if (value == null) return 0;
  if (typeof value === "number") return value;
  const parts = String(value).split(":").map(Number);
  if (parts.length === 3) return parts[0] * 3600 + parts[1] * 60 + parts[2];
  if (parts.length === 2) return parts[0] * 60 + parts[1];
  return Number(parts[0]) || 0;
};
