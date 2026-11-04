import { useEffect, useRef, useState } from "react";
import { Check, Pencil, Trash2, X } from "lucide-react";
import { formatShort } from "@/utils/formatTime";

const splitWords = (text) => String(text || "").trim().split(/\s+/).filter(Boolean);

const round = (value) => Math.round(value * 1000) / 1000;

/**
 * Susun ulang timing kata setelah teks caption diedit.
 * - Jika jumlah kata sama, timing lama dipertahankan 1:1.
 * - Jika berubah, durasi segmen dibagi proporsional berdasarkan panjang kata.
 */
const rebuildWords = (segment, text) => {
  const tokens = splitWords(text);
  if (tokens.length === 0) return [];

  const start = Number(segment.start) || 0;
  const end = Math.max(start, Number(segment.end) || start);
  const oldWords = Array.isArray(segment.words) ? segment.words : [];

  if (oldWords.length === tokens.length) {
    return tokens.map((word, i) => ({
      word,
      start: round(Number(oldWords[i].start) || start),
      end: round(Number(oldWords[i].end) || end),
    }));
  }

  const weights = tokens.map(
    (word) => Math.max(1, word.replace(/[^\p{L}\p{N}]/gu, "").length)
  );
  const totalWeight = weights.reduce((sum, value) => sum + value, 0);

  let cursor = start;
  return tokens.map((word, i) => {
    const isLast = i === tokens.length - 1;
    const span = isLast
      ? end - cursor
      : ((end - start) * weights[i]) / totalWeight;
    const wordStart = cursor;
    const wordEnd = isLast ? end : Math.min(end, cursor + span);
    cursor = wordEnd;
    return { word, start: round(wordStart), end: round(wordEnd) };
  });
};

export const CaptionTrack = ({ captions = [], currentTime = 0, onSelect, onChange }) => {
  const [editingId, setEditingId] = useState(null);
  const [draft, setDraft] = useState("");
  const textareaRef = useRef(null);

  useEffect(() => {
    if (editingId !== null) textareaRef.current?.focus();
  }, [editingId]);

  const startEdit = (caption) => {
    setEditingId(caption.id);
    setDraft(caption.text || "");
  };

  const cancelEdit = () => {
    setEditingId(null);
    setDraft("");
  };

  const saveEdit = (caption) => {
    const text = draft.replace(/\s+/g, " ").trim();
    if (!text) return;
    const updated = { ...caption, text, words: rebuildWords(caption, text) };
    onChange?.(captions.map((item) => (item.id === caption.id ? updated : item)));
    cancelEdit();
  };

  const deleteCaption = (caption) => {
    if (!window.confirm(`Hapus caption "${caption.text}"?`)) return;
    onChange?.(captions.filter((item) => item.id !== caption.id));
    if (editingId === caption.id) cancelEdit();
  };

  return (
    <div className="space-y-2">
      {captions.length > 0 && (
        <p className="text-[11px] text-white/35">
          Klik ikon pensil atau klik dua kali teks untuk memperbaiki kata yang salah
          terdeteksi. Timing kata menyesuaikan otomatis.
        </p>
      )}

      <div className="space-y-2 max-h-64 overflow-y-auto pr-2">
        {captions.map((c) => {
          const isEditing = editingId === c.id;
          const isActive = currentTime >= c.start && currentTime <= c.end;

          return (
            <div
              key={c.id}
              onClick={() => !isEditing && onSelect?.(c)}
              className={`px-3 py-2 rounded-xl border transition ${
                isEditing
                  ? "bg-accent-500/10 border-accent-500/60"
                  : isActive
                    ? "bg-accent-500/20 border-accent-500/50 cursor-pointer"
                    : "bg-white/5 border-white/10 hover:bg-white/10 cursor-pointer"
              }`}
            >
              <div className="flex items-center gap-3">
                <span className="text-xs font-mono text-white/40 w-16 shrink-0">
                  {formatShort(c.start)} – {formatShort(c.end)}
                </span>

                {isEditing ? (
                  <textarea
                    ref={textareaRef}
                    value={draft}
                    onChange={(e) => setDraft(e.target.value)}
                    onKeyDown={(e) => {
                      if (e.key === "Enter") {
                        e.preventDefault();
                        if (draft.trim()) saveEdit(c);
                      } else if (e.key === "Escape") {
                        e.preventDefault();
                        cancelEdit();
                      }
                    }}
                    rows={2}
                    className="flex-1 min-w-0 bg-white/5 border border-accent-500/40 rounded-lg px-2 py-1 text-sm text-white resize-none focus:outline-none focus:ring-2 focus:ring-accent-500"
                  />
                ) : (
                  <span
                    className="flex-1 min-w-0 text-sm text-white truncate"
                    title={c.text}
                    onDoubleClick={() => startEdit(c)}
                  >
                    {c.text}
                  </span>
                )}

                {isEditing ? (
                  <div className="flex items-center gap-1 shrink-0">
                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation();
                        saveEdit(c);
                      }}
                      disabled={!draft.trim()}
                      className="p-1.5 rounded-lg text-green-400 hover:bg-green-500/15 disabled:opacity-30 disabled:hover:bg-transparent"
                      title="Simpan (Enter)"
                      aria-label="Simpan caption"
                    >
                      <Check size={14} />
                    </button>
                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation();
                        cancelEdit();
                      }}
                      className="p-1.5 rounded-lg text-white/50 hover:bg-white/10"
                      title="Batal (Esc)"
                      aria-label="Batal edit"
                    >
                      <X size={14} />
                    </button>
                  </div>
                ) : (
                  <div className="ml-auto flex items-center gap-1 shrink-0">
                    {c.words?.length ? (
                      <span className="text-[10px] text-white/30 bg-white/5 rounded px-2 py-0.5">
                        {c.words.length} kata
                      </span>
                    ) : null}
                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation();
                        startEdit(c);
                      }}
                      className="p-1.5 rounded-lg text-white/40 hover:text-white hover:bg-white/10"
                      title="Edit teks caption"
                      aria-label="Edit caption"
                    >
                      <Pencil size={13} />
                    </button>
                    <button
                      type="button"
                      onClick={(e) => {
                        e.stopPropagation();
                        deleteCaption(c);
                      }}
                      className="p-1.5 rounded-lg text-white/40 hover:text-red-400 hover:bg-red-500/10"
                      title="Hapus caption"
                      aria-label="Hapus caption"
                    >
                      <Trash2 size={13} />
                    </button>
                  </div>
                )}
              </div>

              {isEditing && (
                <p className="text-[10px] text-white/35 mt-1 ml-[76px]">
                  Enter simpan · Esc batal
                </p>
              )}
            </div>
          );
        })}

        {captions.length === 0 && (
          <div className="text-center py-8 text-white/40 text-sm">
            Belum ada caption. Klik "Auto-Caption" untuk menghasilkan transkripsi.
          </div>
        )}
      </div>
    </div>
  );
};
