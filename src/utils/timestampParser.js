/**
 * Parser client-side untuk mem-parsing daftar timestamp / durasi dari teks chat.
 */

export const parseTimeString = (value) => {
  if (value === null || value === undefined) return 0;
  const str = String(value).trim();
  if (!str) return 0;
  if (/^\d+(\.\d+)?$/.test(str)) return parseFloat(str);

  const parts = str.split(":").map(Number);
  if (parts.some(isNaN)) return 0;

  if (parts.length === 3) {
    return parts[0] * 3600 + parts[1] * 60 + parts[2];
  }
  if (parts.length === 2) {
    return parts[0] * 60 + parts[1];
  }
  return parts[0] || 0;
};

export const formatDurationDisplay = (totalSec) => {
  const sec = Math.max(0, Math.round(totalSec || 0));
  const m = Math.floor(sec / 60);
  const s = sec % 60;
  if (m === 0) return `${s} dtk`;
  return `${m} mnt ${s} dtk`;
};

export const parseTimestampText = (text) => {
  if (!text || typeof text !== "string") return [];

  const lines = text.trim().split("\n");
  const segments = [];

  // Pola regex mirip dengan backend
  // [00:00] – [00:30] (Hook/Opening): Menunjukkan hasil akhir...
  const linePattern = /^\s*(?:[-*•]|\d+[\.\)])?\s*\[?\s*(\d{1,2}:\d{2}(?::\d{2})?(?:\.\d+)?)\s*\]?\s*(?:[-–—~]|to|sampai|s\/d|\.{2,})\s*\[?\s*(\d{1,2}:\d{2}(?::\d{2})?(?:\.\d+)?)\s*\]?\s*(?:[:\-–—]\s*)?(?:\(([^)]+)\))?\s*(?:[:\-–—]\s*)?(.*)$/i;

  const fallbackPattern = /\[?\b(\d{1,2}:\d{2}(?::\d{2})?(?:\.\d+)?)\b\]?\s*(?:[-–—~]|to|sampai|s\/d|\.{2,})\s*\[?\b(\d{1,2}:\d{2}(?::\d{2})?(?:\.\d+)?)\b\]?/i;

  for (let i = 0; i < lines.length; i++) {
    const rawLine = lines[i].trim();
    if (!rawLine) continue;

    let startRaw = null;
    let endRaw = null;
    let tag = null;
    let desc = null;

    const match = rawLine.match(linePattern);
    if (match) {
      startRaw = match[1];
      endRaw = match[2];
      tag = match[3] || null;
      desc = match[4] || "";
    } else {
      const subMatch = rawLine.match(fallbackPattern);
      if (!subMatch) continue;

      startRaw = subMatch[1];
      endRaw = subMatch[2];
      const remainder = rawLine.slice(subMatch.index + subMatch[0].length).replace(/^[\s:\-–—]+/, "");
      const tagMatch = remainder.match(/^\(([^)]+)\)\s*:?\s*(.*)$/);
      if (tagMatch) {
        tag = tagMatch[1];
        desc = tagMatch[2];
      } else {
        tag = null;
        desc = remainder;
      }
    }

    const startSec = parseTimeString(startRaw);
    const endSec = parseTimeString(endRaw);

    if (endSec <= startSec) continue;
    const duration = endSec - startSec;
    if (duration < 0.2) continue;

    const cleanTag = tag ? tag.trim() : null;
    const cleanDesc = desc ? desc.replace(/^[\s:\-–—]+/, "").trim() : "";
    const label = cleanTag || (cleanDesc.includes(":") && cleanDesc.split(":")[0].length < 30 ? cleanDesc.split(":")[0].trim() : null);

    segments.push({
      index: segments.length + 1,
      startTime: startRaw,
      endTime: endRaw,
      startSeconds: Number(startSec.toFixed(2)),
      endSeconds: Number(endSec.toFixed(2)),
      duration: Number(duration.toFixed(2)),
      label,
      description: cleanDesc || cleanTag || `Segmen ${segments.length + 1}`,
      rawLine,
    });
  }

  return segments;
};
