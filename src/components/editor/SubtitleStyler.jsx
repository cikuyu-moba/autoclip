import { Palette } from "lucide-react";

const hexToAss = (hex) => {
  const m = /^#?([0-9a-f]{6})$/i.exec(hex || "");
  if (!m) return "&H00FFFFFF";
  const r = m[1].slice(0, 2);
  const g = m[1].slice(2, 4);
  const b = m[1].slice(4, 6);
  return `&H00${b}${g}${r}`.toUpperCase();
};

const assToHex = (ass) => {
  const m = /^&H[0-9A-Fa-f]{2}([0-9A-Fa-f]{2})([0-9A-Fa-f]{2})([0-9A-Fa-f]{2})$/i.exec(
    ass || ""
  );
  if (!m) return "#ffffff";
  return `#${m[3]}${m[2]}${m[1]}`.toLowerCase();
};

export const SubtitleStyler = ({ style, onChange }) => {
  const fontSize = style.font_size ?? 72;
  const outline = style.outline_width ?? 3;
  const popUp = style.pop_up_effect ?? true;

  return (
    <div className="space-y-4">
      <h3 className="text-sm font-semibold text-white flex items-center gap-2">
        <Palette size={16} /> Gaya Subtitle
      </h3>
      <div className="space-y-3">
        <div>
          <label className="text-xs text-white/60 block mb-1">Ukuran Font: {fontSize}</label>
          <input
            type="range"
            min="24"
            max="96"
            value={fontSize}
            onChange={(e) => onChange({ font_size: Number(e.target.value) })}
            className="w-full accent-accent-500"
          />
        </div>
        <div className="grid grid-cols-2 gap-3">
          <div>
            <label className="text-xs text-white/60 block mb-1">Warna Teks</label>
            <input
              type="color"
              value={assToHex(style.primary_color)}
              onChange={(e) => onChange({ primary_color: hexToAss(e.target.value) })}
              className="w-10 h-8 rounded cursor-pointer border-0 bg-transparent"
            />
          </div>
          <div>
            <label className="text-xs text-white/60 block mb-1">Warna Highlight</label>
            <input
              type="color"
              value={assToHex(style.highlight_color)}
              onChange={(e) => onChange({ highlight_color: hexToAss(e.target.value) })}
              className="w-10 h-8 rounded cursor-pointer border-0 bg-transparent"
            />
          </div>
        </div>
        <div>
          <label className="text-xs text-white/60 block mb-1">Tebal Outline: {outline}</label>
          <input
            type="range"
            min="0"
            max="8"
            step="0.5"
            value={outline}
            onChange={(e) => onChange({ outline_width: Number(e.target.value) })}
            className="w-full accent-accent-500"
          />
        </div>
        <div className="flex items-center gap-2">
          <input
            id="popUp"
            type="checkbox"
            checked={popUp}
            onChange={(e) => onChange({ pop_up_effect: e.target.checked })}
            className="accent-accent-500"
          />
          <label htmlFor="popUp" className="text-sm text-white cursor-pointer">
            Efek Pop-Up / Karaoke (Reels/TikTok style)
          </label>
        </div>
      </div>
      <div className="border-t border-white/10 pt-3 text-xs text-white/40">
        Perubahan gaya diterapkan saat Anda klik "Terapkan &amp; Pratinjau".
      </div>
    </div>
  );
};
