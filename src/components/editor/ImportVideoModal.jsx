import { useEffect, useRef, useState } from "react";
import { Upload, FolderOpen, Film, Loader2, Check } from "lucide-react";
import { Modal, Button, Progress } from "@/components/ui";
import { uploadVideo, listLibrary } from "@/api/media";

const formatSize = (bytes) => {
  if (!bytes) return "-";
  const mb = bytes / (1024 * 1024);
  return mb >= 1024 ? `${(mb / 1024).toFixed(1)} GB` : `${mb.toFixed(1)} MB`;
};

const KIND_LABEL = {
  clip: "Clip",
  upload: "Upload",
  export: "Export",
  preview: "Preview",
};

export const ImportVideoModal = ({ isOpen, onClose, onImported }) => {
  const [tab, setTab] = useState("upload");
  const [items, setItems] = useState([]);
  const [loadingList, setLoadingList] = useState(false);
  const [uploading, setUploading] = useState(false);
  const [progress, setProgress] = useState(0);
  const [error, setError] = useState(null);
  const fileRef = useRef(null);

  useEffect(() => {
    if (!isOpen) return;
    setError(null);
    setLoadingList(true);
    listLibrary(60)
      .then((res) => setItems(res.data.items || []))
      .catch((err) => setError(err.response?.data?.detail || err.message))
      .finally(() => setLoadingList(false));
  }, [isOpen]);

  const handleUpload = async (file) => {
    if (!file) return;
    setUploading(true);
    setProgress(0);
    setError(null);
    try {
      const { data } = await uploadVideo(file, setProgress);
      onImported?.(data);
      onClose();
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
    } finally {
      setUploading(false);
      if (fileRef.current) fileRef.current.value = "";
    }
  };

  return (
    <Modal isOpen={isOpen} onClose={() => !uploading && onClose()} title="Import Video ke Editor">
      <div className="space-y-4">
        <div className="flex gap-2">
          <button
            onClick={() => setTab("upload")}
            className={`flex items-center gap-2 px-3 py-2 rounded-xl text-sm border transition ${
              tab === "upload"
                ? "bg-accent-500/15 border-accent-500/40 text-accent-300"
                : "bg-white/5 border-white/10 text-white/60 hover:text-white"
            }`}
          >
            <Upload size={16} /> Upload Baru
          </button>
          <button
            onClick={() => setTab("library")}
            className={`flex items-center gap-2 px-3 py-2 rounded-xl text-sm border transition ${
              tab === "library"
                ? "bg-accent-500/15 border-accent-500/40 text-accent-300"
                : "bg-white/5 border-white/10 text-white/60 hover:text-white"
            }`}
          >
            <FolderOpen size={16} /> Video Tersimpan
          </button>
        </div>

        {tab === "upload" && (
          <div className="space-y-3">
            <input
              ref={fileRef}
              type="file"
              accept="video/mp4,video/quicktime,video/x-matroska,video/webm,video/*"
              disabled={uploading}
              onChange={(e) => handleUpload(e.target.files?.[0])}
              className="w-full text-sm text-white/60 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-accent-500/20 file:text-accent-300 hover:file:bg-accent-500/30 file:cursor-pointer bg-white/5 border border-white/10 rounded-xl p-2"
            />
            <p className="text-xs text-white/40">
              Format: MP4, MOV, MKV, WEBM, M4V. Maksimal 500 MB.
            </p>
            {uploading && (
              <div className="space-y-2">
                <div className="flex justify-between text-xs text-white/60">
                  <span className="flex items-center gap-1.5">
                    <Loader2 size={14} className="animate-spin" /> Mengunggah...
                  </span>
                  <span className="text-accent-400">{progress}%</span>
                </div>
                <Progress value={progress} />
              </div>
            )}
          </div>
        )}

        {tab === "library" && (
          <div className="max-h-[50vh] overflow-y-auto pr-1">
            {loadingList && (
              <div className="flex items-center justify-center py-8 text-white/40 text-sm gap-2">
                <Loader2 size={16} className="animate-spin" /> Memuat daftar video...
              </div>
            )}
            {!loadingList && items.length === 0 && (
              <div className="text-center py-8 text-sm text-white/40">
                Belum ada video tersimpan. Upload video dulu.
              </div>
            )}
            <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
              {items.map((item) => (
                <button
                  key={item.video_url}
                  onClick={() => {
                    onImported?.({ video_url: item.video_url, filename: item.filename });
                    onClose();
                  }}
                  className="flex items-center gap-3 p-2 rounded-xl bg-white/5 border border-white/10 hover:border-accent-500/50 hover:bg-white/10 text-left"
                >
                  <div className="w-10 h-10 rounded-lg bg-accent-500/10 border border-accent-500/20 flex items-center justify-center text-accent-400 shrink-0">
                    <Film size={18} />
                  </div>
                  <div className="min-w-0 flex-1">
                    <div className="text-xs text-white truncate">{item.filename}</div>
                    <div className="text-[10px] text-white/40">
                      {KIND_LABEL[item.kind] || item.kind} • {formatSize(item.size)}
                    </div>
                  </div>
                  <Check size={14} className="text-white/20" />
                </button>
              ))}
            </div>
          </div>
        )}

        {error && (
          <div className="rounded-xl bg-red-500/10 border border-red-500/30 p-3 text-xs text-red-400 whitespace-pre-wrap">
            {error}
          </div>
        )}
      </div>
    </Modal>
  );
};
