import { useState } from "react";
import { Frame, X, FolderOpen } from "lucide-react";
import { AIImageGenerator } from "@/components/ai/AIImageGenerator";
import { AssetPickerModal } from "@/components/editor/AssetPickerModal";

const makeFrame = (src) => ({
  id: `frame-${Date.now()}-${Math.random().toString(16).slice(2, 7)}`,
  src,
  fit: "cover",
  x: 0.5,
  y: 0.5,
  width: 1,
  start_time: 0,
  end_time: null,
  rotate: 0,
});

/**
 * Panel frame: overlay penuh layar yang di-generate AI.
 * Bagian tengah frame transparan sehingga video tetap terlihat.
 */
export const FramePanel = ({ frames = [], onChange, selectedId, onSelect }) => {
  const [assetOpen, setAssetOpen] = useState(false);

  const addFrame = (src) => {
    const frame = makeFrame(src);
    onChange([...frames, frame]);
    onSelect?.(frame.id);
  };

  const remove = (id) => {
    onChange(frames.filter((f) => f.id !== id));
    if (selectedId === id) onSelect?.(null);
  };

  return (
    <div className="space-y-3">
      <div className="flex items-center justify-between gap-2">
        <h3 className="text-sm font-semibold text-white flex items-center gap-2">
          <Frame size={16} /> Frame / Overlay
        </h3>
        <div className="flex flex-wrap items-center justify-end gap-2">
          <button
            onClick={() => setAssetOpen(true)}
            className="flex items-center gap-1.5 px-3 py-1.5 bg-white/5 border border-white/10 rounded-lg text-xs text-white hover:bg-white/10 transition"
            title="Pilih frame yang sudah pernah di-generate"
          >
            <FolderOpen size={14} /> Aset
          </button>
          <AIImageGenerator
            kind="frame"
            label="Frame AI"
            onCreated={(data) => addFrame(data.src)}
          />
        </div>
      </div>

      <div className="grid grid-cols-4 gap-2">
        {frames.map((f) => (
          <div
            key={f.id}
            onClick={() => onSelect?.(selectedId === f.id ? null : f.id)}
            className={`aspect-[9/16] rounded-xl overflow-hidden border bg-[repeating-conic-gradient(#1e293b_0%_25%,#0f172a_0%_50%)] bg-[length:12px_12px] relative group cursor-pointer transition ${
              selectedId === f.id
                ? "border-accent-500 ring-2 ring-accent-500/40"
                : "border-white/10 hover:border-white/30"
            }`}
            title="Klik untuk atur waktu tampil di timeline"
          >
            <img src={f.src} alt="frame" className="w-full h-full object-cover" />
            <button
              className="absolute top-1 right-1 bg-black/60 text-white rounded p-0.5 hover:bg-red-500"
              onClick={(e) => {
                e.stopPropagation();
                remove(f.id);
              }}
            >
              <X size={12} />
            </button>
          </div>
        ))}
      </div>
      {frames.length === 0 && (
        <div className="text-center py-4 text-xs text-white/40">
          Belum ada frame. Generate frame dengan AI atau pilih dari aset lama.
        </div>
      )}

      <AssetPickerModal
        isOpen={assetOpen}
        onClose={() => setAssetOpen(false)}
        kind="frame"
        onSelect={(asset) => addFrame(asset.src)}
      />
    </div>
  );
};
