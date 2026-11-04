import { Clock, Scissors } from "lucide-react";
import { formatShort, toSeconds } from "@/utils/formatTime";

/**
 * Timeline potong sederhana: dua slider (awal & akhir) di atas trek durasi.
 * Nilai start/end dalam detik.
 */
export const TrimTimeline = ({
  duration = 0,
  start = 0,
  end = 0,
  onChange,
  disabled = false,
}) => {
  const max = Math.max(Number(duration) || 0, 1);
  const safeStart = Math.min(Math.max(0, Number(start) || 0), max - 0.5);
  const safeEnd = Math.min(Math.max(safeStart + 0.5, Number(end) || max), max);
  const selDuration = Math.max(0, safeEnd - safeStart);
  const leftPct = (safeStart / max) * 100;
  const widthPct = (selDuration / max) * 100;

  const update = (patch) => onChange?.({ start: safeStart, end: safeEnd, ...patch });

  const commitStart = (value) => {
    const next = Math.min(Math.max(0, value), safeEnd - 0.5);
    update({ start: next });
  };
  const commitEnd = (value) => {
    const next = Math.max(Math.min(value, max), safeStart + 0.5);
    update({ end: next });
  };

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between text-xs text-white/50">
        <span className="flex items-center gap-1.5">
          <Clock size={14} /> Durasi sumber: {formatShort(max)}
        </span>
        <span className="flex items-center gap-1.5 text-accent-400 font-medium">
          <Scissors size={14} /> Potongan: {selDuration.toFixed(1)} detik
        </span>
      </div>

      {/* Trek visual */}
      <div className="relative h-9 bg-white/5 border border-white/10 rounded-lg overflow-hidden">
        <div className="absolute inset-0 flex items-center justify-center text-[10px] text-white/20 tracking-widest">
          TIMELINE
        </div>
        <div
          className="absolute top-0 bottom-0 bg-accent-500/25 border-x-2 border-accent-400"
          style={{ left: `${leftPct}%`, width: `${Math.max(widthPct, 0.5)}%` }}
        />
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <div className="flex items-center justify-between mb-1">
            <label className="text-xs text-white/60">Mulai</label>
            <span className="text-xs font-mono text-white/80">{formatShort(safeStart)}</span>
          </div>
          <input
            type="range"
            min="0"
            max={Math.max(max - 0.5, 0)}
            step="0.1"
            value={safeStart}
            disabled={disabled}
            onChange={(e) => commitStart(Number(e.target.value))}
            className="range-input"
          />
          <input
            type="text"
            defaultValue={formatShort(safeStart)}
            key={`start-${safeStart}`}
            disabled={disabled}
            onBlur={(e) => commitStart(toSeconds(e.target.value))}
            onKeyDown={(e) => {
              if (e.key === "Enter") commitStart(toSeconds(e.currentTarget.value));
            }}
            className="mt-2 w-full bg-white/5 border border-white/10 rounded-lg px-2 py-1 text-xs font-mono text-white focus:outline-none focus:ring-1 focus:ring-accent-500"
          />
        </div>
        <div>
          <div className="flex items-center justify-between mb-1">
            <label className="text-xs text-white/60">Selesai</label>
            <span className="text-xs font-mono text-white/80">{formatShort(safeEnd)}</span>
          </div>
          <input
            type="range"
            min="0.5"
            max={max}
            step="0.1"
            value={safeEnd}
            disabled={disabled}
            onChange={(e) => commitEnd(Number(e.target.value))}
            className="range-input"
          />
          <input
            type="text"
            defaultValue={formatShort(safeEnd)}
            key={`end-${safeEnd}`}
            disabled={disabled}
            onBlur={(e) => commitEnd(toSeconds(e.target.value))}
            onKeyDown={(e) => {
              if (e.key === "Enter") commitEnd(toSeconds(e.currentTarget.value));
            }}
            className="mt-2 w-full bg-white/5 border border-white/10 rounded-lg px-2 py-1 text-xs font-mono text-white focus:outline-none focus:ring-1 focus:ring-accent-500"
          />
        </div>
      </div>

      <div className="flex flex-wrap gap-2">
        {[15, 30, 60].map((seconds) => (
          <button
            key={seconds}
            type="button"
            disabled={disabled || max <= 1}
            onClick={() =>
              update({ start: 0, end: Math.min(seconds, max) })
            }
            className="px-2.5 py-1 rounded-lg text-[11px] bg-white/5 border border-white/10 text-white/60 hover:text-white hover:bg-white/10 disabled:opacity-40"
          >
            {seconds} detik pertama
          </button>
        ))}
        <button
          type="button"
          disabled={disabled || max <= 1}
          onClick={() => update({ start: 0, end: max })}
          className="px-2.5 py-1 rounded-lg text-[11px] bg-white/5 border border-white/10 text-white/60 hover:text-white hover:bg-white/10 disabled:opacity-40"
        >
          Seluruh video
        </button>
      </div>
    </div>
  );
};
