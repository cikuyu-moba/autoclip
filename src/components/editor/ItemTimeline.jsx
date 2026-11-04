import { Trash2, Move, Timer, Maximize2, RotateCw } from "lucide-react";
import { formatShort } from "@/utils/formatTime";

const clamp = (value, min, max) => Math.min(Math.max(value, min), max);

/**
 * Timeline untuk stiker / frame: atur kapan muncul, berapa lama tampil,
 * serta posisi & ukuran di layar.
 */
export const ItemTimeline = ({
  title = "Timeline",
  icon = null,
  items = [],
  duration = 0,
  selectedId = null,
  onSelect,
  onChange,
  positionControls = true,
  emptyText = "Belum ada item.",
}) => {
  const total = Math.max(Number(duration) || 0, 0.1);
  const selected = items.find((item) => item.id === selectedId) || null;

  const itemEnd = (item) =>
    item.end_time == null ? total : Math.min(Number(item.end_time) || 0, total);

  const updateSelected = (patch) => {
    if (!selected) return;
    onChange?.(
      items.map((item) => (item.id === selected.id ? { ...item, ...patch } : item))
    );
  };

  const setStart = (value) => {
    if (!selected) return;
    const start = clamp(value, 0, Math.max(total - 0.3, 0));
    const currentEnd = itemEnd(selected);
    const end = Math.max(start + 0.3, currentEnd);
    updateSelected({ start_time: start, end_time: Math.min(end, total) });
  };

  const setDisplayDuration = (value) => {
    if (!selected) return;
    const start = Number(selected.start_time) || 0;
    const dur = clamp(value, 0.3, Math.max(total - start, 0.3));
    updateSelected({ end_time: Math.min(start + dur, total) });
  };

  const removeSelected = () => {
    if (!selected) return;
    onChange?.(items.filter((item) => item.id !== selected.id));
    onSelect?.(null);
  };

  const displayDuration = selected
    ? Math.max(0.1, itemEnd(selected) - (Number(selected.start_time) || 0))
    : 0;

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between">
        <h4 className="text-xs font-semibold text-white/80 flex items-center gap-1.5">
          {icon} {title}
        </h4>
        <span className="text-[10px] text-white/30">{items.length} item</span>
      </div>

      {items.length === 0 ? (
        <div className="text-center py-3 text-[11px] text-white/30 border border-dashed border-white/10 rounded-lg">
          {emptyText}
        </div>
      ) : (
        <div className="space-y-1.5">
          {items.map((item) => {
            const start = clamp(Number(item.start_time) || 0, 0, total);
            const end = itemEnd(item);
            const left = (start / total) * 100;
            const width = Math.max(((end - start) / total) * 100, 1.5);
            const active = selectedId === item.id;

            return (
              <div key={item.id} className="flex items-center gap-2">
                <img
                  src={item.src}
                  alt=""
                  className="w-6 h-6 rounded object-contain bg-white/5 border border-white/10 shrink-0"
                />
                <button
                  type="button"
                  onClick={() => onSelect?.(active ? null : item.id)}
                  className={`relative flex-1 h-7 rounded-lg overflow-hidden border transition ${
                    active
                      ? "border-accent-500 bg-accent-500/10"
                      : "border-white/10 bg-white/5 hover:bg-white/10"
                  }`}
                  title={`${formatShort(start)} – ${formatShort(end)}`}
                >
                  <span
                    className={`absolute top-1 bottom-1 rounded ${
                      active
                        ? "bg-accent-500/60 border border-accent-400"
                        : "bg-white/20 border border-white/20"
                    }`}
                    style={{ left: `${left}%`, width: `${width}%` }}
                  />
                  <span className="absolute inset-y-0 left-1.5 flex items-center text-[9px] font-mono text-white/70">
                    {formatShort(start)}→{formatShort(end)}
                  </span>
                </button>
              </div>
            );
          })}
        </div>
      )}

      {selected && (
        <div className="rounded-xl border border-white/10 bg-white/5 p-3 space-y-3">
          <div className="flex items-center justify-between">
            <span className="text-[11px] font-medium text-white/70">
              Atur: {formatShort(selected.start_time || 0)} – {formatShort(itemEnd(selected))}
            </span>
            <button
              type="button"
              onClick={removeSelected}
              className="flex items-center gap-1 text-[11px] text-red-400 hover:text-red-300"
            >
              <Trash2 size={12} /> Hapus
            </button>
          </div>

          <div>
            <label className="flex items-center justify-between text-[11px] text-white/50 mb-1">
              <span className="flex items-center gap-1">
                <Timer size={12} /> Mulai tampil
              </span>
              <span className="font-mono text-white/80">{formatShort(selected.start_time || 0)}</span>
            </label>
            <input
              type="range"
              min="0"
              max={Math.max(total - 0.3, 0)}
              step="0.1"
              value={Number(selected.start_time) || 0}
              onChange={(e) => setStart(Number(e.target.value))}
              className="range-input"
            />
          </div>

          <div>
            <label className="flex items-center justify-between text-[11px] text-white/50 mb-1">
              <span>Durasi tampil</span>
              <span className="font-mono text-white/80">{displayDuration.toFixed(1)} detik</span>
            </label>
            <input
              type="range"
              min="0.3"
              max={Math.max(total - (Number(selected.start_time) || 0), 0.3)}
              step="0.1"
              value={displayDuration}
              onChange={(e) => setDisplayDuration(Number(e.target.value))}
              className="range-input"
            />
          </div>

          {positionControls && (
            <>
              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="flex items-center justify-between text-[11px] text-white/50 mb-1">
                    <span className="flex items-center gap-1"><Move size={12} /> Horizontal</span>
                    <span className="font-mono text-white/80">{Math.round((selected.x ?? 0.5) * 100)}%</span>
                  </label>
                  <input
                    type="range"
                    min="0"
                    max="1"
                    step="0.01"
                    value={selected.x ?? 0.5}
                    onChange={(e) => updateSelected({ x: Number(e.target.value) })}
                    className="range-input"
                  />
                </div>
                <div>
                  <label className="flex items-center justify-between text-[11px] text-white/50 mb-1">
                    <span>Vertikal</span>
                    <span className="font-mono text-white/80">{Math.round((selected.y ?? 0.5) * 100)}%</span>
                  </label>
                  <input
                    type="range"
                    min="0"
                    max="1"
                    step="0.01"
                    value={selected.y ?? 0.5}
                    onChange={(e) => updateSelected({ y: Number(e.target.value) })}
                    className="range-input"
                  />
                </div>
              </div>

              <div className="grid grid-cols-2 gap-3">
                <div>
                  <label className="flex items-center justify-between text-[11px] text-white/50 mb-1">
                    <span className="flex items-center gap-1"><Maximize2 size={12} /> Ukuran</span>
                    <span className="font-mono text-white/80">{Math.round((selected.width ?? 0.2) * 100)}%</span>
                  </label>
                  <input
                    type="range"
                    min="0.04"
                    max="1"
                    step="0.01"
                    value={selected.width ?? 0.2}
                    onChange={(e) => updateSelected({ width: Number(e.target.value) })}
                    className="range-input"
                  />
                </div>
                <div>
                  <label className="flex items-center justify-between text-[11px] text-white/50 mb-1">
                    <span className="flex items-center gap-1"><RotateCw size={12} /> Rotasi</span>
                    <span className="font-mono text-white/80">{Math.round(selected.rotate ?? 0)}°</span>
                  </label>
                  <input
                    type="range"
                    min="-180"
                    max="180"
                    step="1"
                    value={selected.rotate ?? 0}
                    onChange={(e) => updateSelected({ rotate: Number(e.target.value) })}
                    className="range-input"
                  />
                </div>
              </div>
            </>
          )}

          <button
            type="button"
            onClick={() => updateSelected({ start_time: 0, end_time: null })}
            className="w-full py-1.5 rounded-lg text-[11px] bg-white/5 border border-white/10 text-white/60 hover:text-white hover:bg-white/10"
          >
            Tampilkan sepanjang video
          </button>
        </div>
      )}
    </div>
  );
};
