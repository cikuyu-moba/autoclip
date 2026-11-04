import { useState } from "react";
import { Sticker, Plus, X, FolderOpen } from "lucide-react";
import { AIImageGenerator } from "@/components/ai/AIImageGenerator";
import { AssetPickerModal } from "@/components/editor/AssetPickerModal";

const STICKERS = [
  { id: "fire", src: "/storage/assets/sticker_fire.png", name: "Lingkaran Merah" },
  { id: "heart", src: "/storage/assets/sticker_heart.png", name: "Hati" },
  { id: "star", src: "/storage/assets/sticker_star.png", name: "Bintang" },
];

const makeSticker = ({ src, defaultY = 0.78, defaultWidth = 0.2 }) => ({
  id: `sticker-${Date.now()}-${Math.random().toString(16).slice(2, 7)}`,
  src,
  x: 0.5,
  y: defaultY,
  width: defaultWidth,
  start_time: 0,
  end_time: null,
  rotate: 0,
});

export const StickerPanel = ({ stickers = [], onChange, selectedId, onSelect }) => {
  const [open, setOpen] = useState(false);
  const [assetOpen, setAssetOpen] = useState(false);

  const addSticker = (asset) => {
    const sticker = makeSticker({ src: asset.src });
    onChange([...stickers, sticker]);
    onSelect?.(sticker.id);
    setOpen(false);
  };

  const remove = (id) => {
    onChange(stickers.filter((s) => s.id !== id));
    if (selectedId === id) onSelect?.(null);
  };

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between gap-2">
        <h3 className="text-sm font-semibold text-white flex items-center gap-2">
          <Sticker size={16} /> Stiker
        </h3>
        <div className="flex flex-wrap items-center justify-end gap-2">
          <AIImageGenerator
            kind="sticker"
            label="Stiker AI"
            onCreated={(data) => {
              const sticker = makeSticker({ src: data.src });
              onChange([...stickers, sticker]);
              onSelect?.(sticker.id);
            }}
          />
          <button
            onClick={() => setAssetOpen(true)}
            className="flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-xs text-white hover:bg-white/10 transition"
            title="Pilih stiker yang sudah pernah di-generate"
          >
            <FolderOpen size={14} /> Aset
          </button>
          <button
            onClick={() => setOpen(true)}
            className="flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-xs text-white hover:bg-white/10 transition"
          >
            <Plus size={14} /> Stiker
          </button>
        </div>
      </div>

      <div className="grid grid-cols-4 gap-2">
        {stickers.map((s) => (
          <div
            key={s.id}
            onClick={() => onSelect?.(selectedId === s.id ? null : s.id)}
            className={`aspect-square rounded-xl overflow-hidden border bg-white/5 relative group cursor-pointer transition ${
              selectedId === s.id
                ? "border-accent-500 ring-2 ring-accent-500/40"
                : "border-white/10 hover:border-white/30"
            }`}
            title="Klik untuk atur posisi & durasi di timeline"
          >
            <img src={s.src} alt="stiker" className="w-full h-full object-contain p-1" />
            <button
              className="absolute top-1 right-1 bg-black/50 text-white rounded p-0.5 hover:bg-red-500"
              onClick={(e) => {
                e.stopPropagation();
                remove(s.id);
              }}
            >
              <X size={12} />
            </button>
          </div>
        ))}
      </div>
      {stickers.length === 0 && (
        <div className="text-center py-4 text-xs text-white/40">
          Belum ada stiker. Tambahkan stiker bawaan atau generate dengan AI.
        </div>
      )}

      {open && (
        <div className="fixed inset-0 z-50 bg-black/80 backdrop-blur flex items-center justify-center">
          <div className="bg-dark-900 border border-white/10 rounded-2xl p-5 w-full max-w-md">
            <h3 className="font-semibold text-white mb-4">Pilih Stiker</h3>
            <div className="grid grid-cols-3 gap-3 mb-4">
              {STICKERS.map((a) => (
                <button
                  key={a.id}
                  onClick={() => addSticker(a)}
                  className="aspect-square rounded-lg border border-white/10 overflow-hidden bg-white/5 p-2 hover:border-accent-500"
                >
                  <img src={a.src} alt={a.name} className="w-full h-full object-contain" />
                </button>
              ))}
            </div>
            <button
              onClick={() => setOpen(false)}
              className="w-full py-2 rounded-lg border border-white/10 text-white hover:bg-white/5"
            >
              Batal
            </button>
          </div>
        </div>
      )}

      <AssetPickerModal
        isOpen={assetOpen}
        onClose={() => setAssetOpen(false)}
        kind="sticker"
        onSelect={(asset) => {
          const sticker = makeSticker({ src: asset.src });
          onChange([...stickers, sticker]);
          onSelect?.(sticker.id);
        }}
      />
    </div>
  );
};
