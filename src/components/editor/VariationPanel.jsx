import { Music2, Shuffle, Sparkles } from "lucide-react";

export const DEFAULT_VARIATION = { audio: false, video: false, intensity: 50 };

const ToggleRow = ({ checked, onChange, icon, title, description }) => (
  <label
    className={`flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition ${
      checked
        ? "bg-accent-500/10 border-accent-500/40"
        : "bg-white/5 border-white/10 hover:bg-white/10"
    }`}
  >
    <input
      type="checkbox"
      checked={checked}
      onChange={(e) => onChange(e.target.checked)}
      className="mt-0.5 accent-accent-500"
    />
    <div className="min-w-0">
      <span className="text-sm text-white flex items-center gap-1.5">
        {icon} {title}
      </span>
      <span className="text-[11px] text-white/40 block mt-0.5">{description}</span>
    </div>
  </label>
);

export const VariationPanel = ({ variation, onChange }) => {
  const value = { ...DEFAULT_VARIATION, ...(variation || {}) };
  const update = (patch) => onChange?.({ ...value, ...patch });
  const intensity = Number(value.intensity) || 0;
  const active = value.audio || value.video;

  return (
    <div className="border-t border-white/10 pt-4 space-y-3">
      <h3 className="text-sm font-semibold text-white flex items-center gap-2">
        <Shuffle size={16} /> Anti-Duplikat
      </h3>
      <p className="text-xs text-white/45">
        Terapkan variasi acak tiap render agar audio &amp; warna video sedikit berbeda dari
        aslinya.
      </p>

      <ToggleRow
        checked={value.audio}
        onChange={(checked) => update({ audio: checked })}
        icon={<Music2 size={14} />}
        title="Variasi audio"
        description="Pitch & frekuensi digeser acak, durasi tetap."
      />
      <ToggleRow
        checked={value.video}
        onChange={(checked) => update({ video: checked })}
        icon={<Sparkles size={14} />}
        title="Variasi video"
        description="Hue, warna, kontras, dan grain diubah sedikit."
      />

      <div className={active ? "" : "opacity-40 pointer-events-none"}>
        <label className="text-xs text-white/60 block mb-1">
          Intensitas: {intensity}%
        </label>
        <input
          type="range"
          min="10"
          max="100"
          step="5"
          value={intensity}
          onChange={(e) => update({ intensity: Number(e.target.value) })}
          className="w-full accent-accent-500"
        />
        <div className="flex justify-between text-[10px] text-white/30 mt-0.5">
          <span>Halus</span>
          <span>Kuat</span>
        </div>
      </div>

      <p className="text-[10px] text-white/30">
        Nilai variasi diacak ulang setiap render. Ini bukan jaminan lolos deteksi platform.
      </p>
    </div>
  );
};
