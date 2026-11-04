import { useState, useMemo } from "react";
import {
  parseTimestampText,
  formatDurationDisplay,
} from "@/utils/timestampParser";
import { startMultiClip } from "@/api/clipping";
import { waitForJob } from "@/api/jobs";
import { Button, Progress } from "@/components/ui";
import {
  MessageSquareText,
  Scissors,
  Sparkles,
  Trash2,
  Clock,
  Layers,
  CheckCircle2,
  AlertCircle,
  Loader2,
  RotateCcw,
} from "lucide-react";

const SAMPLE_CHAT_INPUT = `[00:00] – [00:30] (Hook/Opening): Menunjukkan hasil akhir tampilan Windows 11 yang berubah jadi keren, minimalis, dan informasi penggunaan RAM yang hanya 2 MB.
[03:14] – [03:41]: Proses cepat pengunduhan dan instalasi aplikasi Windhawk.
[04:16] – [05:05]: Penerapan kode Taskbar di Windhawk yang langsung mengubah tampilan taskbar bawaan secara instan.
[09:44] – [10:08]: Demonstrasi animasi minimize ala macOS yang sangat smooth.
[11:23] – [11:44]: Pengecekan Task Manager untuk membuktikan bahwa penggunaan RAM dari aplikasi ini sangat kecil (hanya 2 MB).`;

export const MultiSegmentClipper = ({
  sourceType, // 'url' | 'upload'
  url,
  local,
  resolution,
  aspect,
  vbr,
  abr,
  onStartEditing,
}) => {
  const [inputText, setInputText] = useState("");
  const [removedIndexes, setRemovedIndexes] = useState(new Set());
  const [busy, setBusy] = useState(false);
  const [progress, setProgress] = useState(0);
  const [statusMessage, setStatusMessage] = useState("");
  const [error, setError] = useState(null);

  // Parse secara real-time dari input text
  const rawSegments = useMemo(() => {
    return parseTimestampText(inputText);
  }, [inputText]);

  // Filter segmen yang dihapus secara manual oleh user
  const activeSegments = useMemo(() => {
    return rawSegments.filter((seg) => !removedIndexes.has(seg.index));
  }, [rawSegments, removedIndexes]);

  const totalDuration = useMemo(() => {
    return activeSegments.reduce((acc, curr) => acc + curr.duration, 0);
  }, [activeSegments]);

  const handleUseSample = () => {
    setInputText(SAMPLE_CHAT_INPUT);
    setRemovedIndexes(new Set());
    setError(null);
  };

  const handleClear = () => {
    setInputText("");
    setRemovedIndexes(new Set());
    setError(null);
  };

  const handleToggleRemove = (index) => {
    setRemovedIndexes((prev) => {
      const next = new Set(prev);
      if (next.has(index)) {
        next.delete(index);
      } else {
        next.add(index);
      }
      return next;
    });
  };

  const handleProcessMultiClip = async () => {
    if (activeSegments.length === 0) {
      setError("Masukkan setidaknya satu durasi/segmen yang valid dari chat.");
      return;
    }

    if (sourceType === "url" && !url?.trim()) {
      setError("Harap masukkan URL video sumber terlebih dahulu.");
      return;
    }

    if (sourceType === "upload" && !local?.video_url) {
      setError("Harap unggah video sumber terlebih dahulu.");
      return;
    }

    setBusy(true);
    setProgress(5);
    setStatusMessage("Menyiapkan pemotongan beberapa segmen...");
    setError(null);

    try {
      const payload = {
        url: sourceType === "url" ? url.trim() : null,
        video_url: sourceType === "upload" ? local.video_url : null,
        segments: activeSegments.map((s) => ({
          start_time: s.startTime,
          end_time: s.endTime,
          label: s.label,
          description: s.description,
        })),
        resolution,
        aspect_ratio: aspect,
        video_bitrate: vbr,
        audio_bitrate: abr,
      };

      const res = await startMultiClip(payload);
      const jobId = res.data?.id || res.data?.job_id;
      if (!jobId) throw new Error("Gagal memulai proses pemotongan segmen");

      const final = await waitForJob(jobId, {
        interval: 1200,
        onUpdate: (job) => {
          setProgress(Math.max(5, job.progress || 0));
          if (job.message) {
            setStatusMessage(job.message);
          } else if (job.progress >= 30 && job.progress < 85) {
            setStatusMessage("Sedang memotong dan merender tiap segmen...");
          } else if (job.progress >= 85) {
            setStatusMessage("Menggabungkan seluruh segmen jadi 1 video...");
          }
        },
      });

      if (final.status === "failed") {
        throw new Error(final.error || "Gagal membuat video rangkuman");
      }

      const clipUrl = final.clip_url || final.result_url;
      if (!clipUrl) throw new Error("File hasil potongan tidak ditemukan");

      setStatusMessage("Selesai! Mengarahkan ke AI Editor...");
      setProgress(100);

      onStartEditing?.({
        clip_url: clipUrl,
        job_id: jobId,
        duration: final.duration || totalDuration,
        title: sourceType === "upload" ? local?.filename : "Rangkuman Multi-Clip",
      });
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="space-y-6">
      {/* Input Text Box / Chat Input */}
      <div className="space-y-2">
        <div className="flex items-center justify-between">
          <label className="text-sm font-semibold text-white/90 flex items-center gap-2">
            <MessageSquareText size={18} className="text-accent-400" />
            Paste Daftar Durasi dari Chat / Rangkuman
          </label>
          <div className="flex items-center gap-2">
            <button
              type="button"
              onClick={handleUseSample}
              disabled={busy}
              className="text-xs text-accent-400 hover:text-accent-300 transition flex items-center gap-1 px-2.5 py-1 bg-accent-500/10 border border-accent-500/20 rounded-lg"
            >
              <Sparkles size={13} />
              Isi Contoh Format
            </button>
            {inputText && (
              <button
                type="button"
                onClick={handleClear}
                disabled={busy}
                className="text-xs text-white/40 hover:text-white transition flex items-center gap-1 px-2 py-1"
              >
                <RotateCcw size={13} />
                Reset
              </button>
            )}
          </div>
        </div>

        <textarea
          rows={6}
          disabled={busy}
          value={inputText}
          onChange={(e) => {
            setInputText(e.target.value);
            setRemovedIndexes(new Set());
          }}
          placeholder={`Paste teks dari chat di sini. Contoh format:\n[00:00] – [00:30] (Hook/Opening): Menunjukkan hasil akhir...\n[03:14] – [03:41]: Proses cepat pengunduhan dan instalasi...\n[04:16] – [05:05]: Penerapan kode Taskbar...`}
          className="w-full bg-dark-900/90 border border-white/15 focus:border-accent-500 focus:ring-1 focus:ring-accent-500 rounded-xl p-3.5 text-sm text-white placeholder-white/25 font-mono leading-relaxed resize-y outline-none transition"
        />

        <p className="text-[11px] text-white/40 leading-relaxed">
          Mendukung berbagai format timestamp dari chat AI (ChatGPT, Claude, dsb.):{" "}
          <code className="text-accent-300 bg-white/5 px-1 py-0.5 rounded">[00:00] – [00:30]</code>,{" "}
          <code className="text-accent-300 bg-white/5 px-1 py-0.5 rounded">03:14 - 03:41</code>, dengan atau tanpa tag{" "}
          <code className="text-accent-300 bg-white/5 px-1 py-0.5 rounded">(Hook)</code> dan keterangan setelahnya.
        </p>
      </div>

      {/* Segments Preview / Detection Summary */}
      {rawSegments.length > 0 ? (
        <div className="space-y-3 bg-white/[0.02] border border-white/10 rounded-2xl p-4">
          <div className="flex flex-wrap items-center justify-between gap-3 border-b border-white/10 pb-3">
            <div className="flex items-center gap-2">
              <span className="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-green-500/15 border border-green-500/30 text-green-400 text-xs font-semibold">
                <CheckCircle2 size={13} />
                {activeSegments.length} Segmen Terdeteksi
              </span>
              {removedIndexes.size > 0 && (
                <span className="text-xs text-white/40">
                  ({removedIndexes.size} diabaikan)
                </span>
              )}
            </div>

            <div className="flex items-center gap-1.5 text-xs text-white/80 bg-white/5 px-3 py-1 rounded-lg border border-white/10">
              <Clock size={13} className="text-accent-400" />
              <span>Total Durasi Video Jadi:</span>
              <strong className="text-accent-300 font-mono">
                {formatDurationDisplay(totalDuration)}
              </strong>
            </div>
          </div>

          {/* List of segment cards */}
          <div className="space-y-2 max-h-[300px] overflow-y-auto pr-1 custom-scrollbar">
            {rawSegments.map((seg) => {
              const isExcluded = removedIndexes.has(seg.index);
              return (
                <div
                  key={seg.index}
                  className={`flex items-start justify-between gap-3 p-3 rounded-xl border text-sm transition ${
                    isExcluded
                      ? "bg-white/[0.01] border-white/5 opacity-40 line-through"
                      : "bg-white/5 border-white/10 hover:border-white/20"
                  }`}
                >
                  <div className="min-w-0 flex-1 space-y-1">
                    <div className="flex flex-wrap items-center gap-2">
                      <span className="px-2 py-0.5 bg-accent-500/20 text-accent-300 text-xs font-bold rounded-md font-mono">
                        #{seg.index}
                      </span>
                      <span className="text-xs font-mono font-semibold text-white/90 bg-white/10 px-2 py-0.5 rounded-md">
                        {seg.startTime} &rarr; {seg.endTime}
                      </span>
                      <span className="text-[11px] text-white/50">
                        ({seg.duration}s)
                      </span>
                      {seg.label && (
                        <span className="px-2 py-0.5 bg-purple-500/20 border border-purple-500/30 text-purple-300 text-[11px] rounded-md font-medium">
                          {seg.label}
                        </span>
                      )}
                    </div>
                    {seg.description && (
                      <p className="text-xs text-white/70 line-clamp-2 leading-relaxed">
                        {seg.description}
                      </p>
                    )}
                  </div>

                  <button
                    type="button"
                    onClick={() => handleToggleRemove(seg.index)}
                    disabled={busy}
                    title={isExcluded ? "Pulihkan segmen" : "Hapus segmen ini"}
                    className={`p-1.5 rounded-lg border transition ${
                      isExcluded
                        ? "bg-green-500/10 border-green-500/30 text-green-400 hover:bg-green-500/20"
                        : "bg-red-500/10 border-red-500/20 text-red-400 hover:bg-red-500/25"
                    }`}
                  >
                    {isExcluded ? (
                      <CheckCircle2 size={14} />
                    ) : (
                      <Trash2 size={14} />
                    )}
                  </button>
                </div>
              );
            })}
          </div>
        </div>
      ) : (
        inputText.trim() && (
          <div className="flex items-center gap-2 p-3 bg-yellow-500/10 border border-yellow-500/20 rounded-xl text-yellow-300 text-xs">
            <AlertCircle size={15} />
            <span>
              Format timestamp belum terdeteksi. Pastikan mencantumkan rentang waktu, misal:{" "}
              <b className="font-mono">[00:00] – [00:30]</b> atau <b className="font-mono">03:14 - 03:41</b>.
            </span>
          </div>
        )
      )}

      {/* Status & Progress */}
      {busy && (
        <div className="rounded-xl bg-white/5 border border-white/10 p-4 space-y-2">
          <div className="flex justify-between text-xs">
            <span className="text-white/80 flex items-center gap-2 font-medium">
              <Loader2 size={14} className="animate-spin text-accent-400" />
              {statusMessage || "Memproses..."}
            </span>
            <span className="text-accent-400 font-mono font-bold">{progress}%</span>
          </div>
          <Progress value={progress} />
        </div>
      )}

      {/* Error display */}
      {error && (
        <div className="rounded-xl bg-red-500/10 border border-red-500/30 p-3.5 text-red-400 text-xs flex items-start gap-2 whitespace-pre-wrap">
          <AlertCircle size={16} className="shrink-0 mt-0.5" />
          <span>{error}</span>
        </div>
      )}

      {/* Action Button */}
      <div className="flex flex-col sm:flex-row gap-3">
        <Button
          variant="primary"
          onClick={handleProcessMultiClip}
          disabled={busy || activeSegments.length === 0}
          className="flex-1 py-3 flex items-center justify-center gap-2 text-sm font-semibold shadow-lg shadow-accent-500/10"
        >
          {busy ? (
            <Loader2 size={18} className="animate-spin" />
          ) : (
            <Scissors size={18} />
          )}
          {busy
            ? "Sedang Memotong & Menggabungkan..."
            : activeSegments.length > 0
            ? `Potong & Satukan Jadi 1 Video (${activeSegments.length} Segmen • ${formatDurationDisplay(totalDuration)})`
            : "Potong & Satukan Jadi 1 Video"}
        </Button>
      </div>
    </div>
  );
};
