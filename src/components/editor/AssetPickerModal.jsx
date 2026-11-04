import { useEffect, useMemo, useState } from "react";
import { ImageOff, Loader2, RefreshCw, Search } from "lucide-react";
import { Modal, Button } from "@/components/ui";
import { listAssets } from "@/api/media";
import { mediaUrl } from "@/utils/media";

/**
 * Modal untuk memilih stiker/frame yang sudah pernah di-generate.
 * `onSelect` menerima objek aset { id, src, name, kind, size, modified }.
 */
export const AssetPickerModal = ({ isOpen, onClose, kind = "sticker", onSelect }) => {
  const [items, setItems] = useState([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState(null);
  const [selected, setSelected] = useState(null);
  const [query, setQuery] = useState("");

  const isFrame = kind === "frame";
  const title = isFrame ? "Pilih Frame" : "Pilih Stiker";

  const load = async () => {
    setLoading(true);
    setError(null);
    try {
      const { data } = await listAssets(kind);
      setItems(data.items || []);
    } catch (err) {
      setItems([]);
      setError(err.response?.data?.detail || err.message);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    if (!isOpen) return;
    setSelected(null);
    setQuery("");
    load();
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [isOpen, kind]);

  const filtered = useMemo(() => {
    const term = query.trim().toLowerCase();
    if (!term) return items;
    return items.filter((item) => item.name.toLowerCase().includes(term));
  }, [items, query]);

  const handleUse = () => {
    if (!selected) return;
    onSelect?.(selected);
    onClose?.();
  };

  return (
    <Modal isOpen={isOpen} onClose={onClose} title={title}>
      <div className="space-y-4">
        <p className="text-sm text-white/50">
          {isFrame
            ? "Aset frame dari hasil generate AI sebelumnya. Pilih salah satu untuk dipakai lagi."
            : "Aset stiker dari hasil generate AI sebelumnya. Pilih salah satu untuk dipakai lagi."}
        </p>

        <div className="flex items-center gap-2">
          <div className="relative flex-1">
            <Search
              size={14}
              className="absolute left-3 top-1/2 -translate-y-1/2 text-white/30"
            />
            <input
              value={query}
              onChange={(e) => setQuery(e.target.value)}
              placeholder="Cari nama aset..."
              className="w-full bg-white/5 border border-white/10 rounded-xl pl-8 pr-3 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-accent-500"
            />
          </div>
          <Button
            variant="ghost"
            onClick={load}
            disabled={loading}
            className="px-3 py-2"
            title="Muat ulang daftar aset"
          >
            <RefreshCw size={14} className={loading ? "animate-spin" : ""} />
          </Button>
        </div>

        {error && (
          <div className="rounded-xl bg-red-500/10 border border-red-500/30 p-3 text-xs text-red-400 whitespace-pre-wrap">
            {error}
          </div>
        )}

        {loading ? (
          <div className="flex items-center justify-center gap-2 py-10 text-sm text-white/50">
            <Loader2 size={16} className="animate-spin" /> Memuat aset...
          </div>
        ) : filtered.length === 0 ? (
          <div className="flex flex-col items-center justify-center gap-2 py-10 text-center text-sm text-white/40">
            <ImageOff size={28} />
            <p>
              {items.length === 0
                ? `Belum ada ${isFrame ? "frame" : "stiker"} yang tersimpan.`
                : "Tidak ada aset yang cocok dengan pencarian."}
            </p>
            {items.length === 0 && (
              <p className="text-[11px] text-white/30">
                Generate {isFrame ? "frame" : "stiker"} dengan AI dulu, lalu asetnya muncul di sini.
              </p>
            )}
          </div>
        ) : (
          <div className="grid grid-cols-3 sm:grid-cols-4 gap-2 max-h-[45vh] overflow-y-auto pr-1">
            {filtered.map((asset) => (
              <button
                key={asset.id}
                type="button"
                onClick={() => setSelected(asset)}
                onDoubleClick={() => {
                  onSelect?.(asset);
                  onClose?.();
                }}
                title={`${asset.name} (klik untuk pilih)`}
                className={`relative overflow-hidden rounded-xl border bg-[repeating-conic-gradient(#1e293b_0%_25%,#0f172a_0%_50%)] bg-[length:12px_12px] transition ${
                  isFrame ? "aspect-[9/16]" : "aspect-square"
                } ${
                  selected?.id === asset.id
                    ? "border-accent-500 ring-2 ring-accent-500/40"
                    : "border-white/10 hover:border-white/30"
                }`}
              >
                <img
                  src={mediaUrl(asset.src)}
                  alt={asset.name}
                  loading="lazy"
                  className={`w-full h-full ${isFrame ? "object-cover" : "object-contain p-1"}`}
                />
              </button>
            ))}
          </div>
        )}

        <div className="flex items-center justify-between gap-2 border-t border-white/10 pt-4">
          <div className="min-w-0 text-xs text-white/40 truncate">
            {selected ? selected.name : `${filtered.length} aset tersedia`}
          </div>
          <div className="flex items-center gap-2 shrink-0">
            <Button variant="ghost" onClick={onClose} className="px-4 py-2">
              Batal
            </Button>
            <Button
              variant="primary"
              onClick={handleUse}
              disabled={!selected}
              className="px-4 py-2"
            >
              Gunakan
            </Button>
          </div>
        </div>
      </div>
    </Modal>
  );
};
