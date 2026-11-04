import { useRef, useState } from "react";
import { waitForJob } from "@/api/jobs";
import { startClip } from "@/api/clipping";
import { getMediaInfo, startPreview, trimLocal, uploadVideo } from "@/api/media";
import { mediaUrl } from "@/utils/media";
import {
  RESOLUTION_OPTIONS,
  ASPECT_RATIO_OPTIONS,
  VIDEO_BITRATE_OPTIONS,
  AUDIO_BITRATE_OPTIONS,
} from "@/utils/ffmpegHelpers";
import { formatShort } from "@/utils/formatTime";
import { TrimTimeline } from "@/components/editor/TrimTimeline";
import { Button, Input, Progress, Select } from "@/components/ui";
import {
  Play,
  Layers,
  Film,
  Download,
  Upload,
  Link2,
  Eye,
  Scissors,
  Loader2,
  Wand2,
  FileVideo,
  CheckCircle2,
  Info,
  MessageSquareText,
} from "lucide-react";
import { MultiSegmentClipper } from "./MultiSegmentClipper";

const sec = (value) => Number(value || 0).toFixed(2);

export const ClipForm = ({ onStartEditing }) => {
  const [mode, setMode] = useState("url"); // 'url' | 'upload'
  const [clipMethod, setClipMethod] = useState("single"); // 'single' | 'multi'

  // URL
  const [url, setUrl] = useState("");
  const [info, setInfo] = useState(null);
  const [infoLoading, setInfoLoading] = useState(false);

  // Upload lokal
  const [local, setLocal] = useState(null);
  const [uploading, setUploading] = useState(false);
  const [uploadProgress, setUploadProgress] = useState(0);

  // Rentang potong
  const [duration, setDuration] = useState(0);
  const [start, setStart] = useState(0);
  const [end, setEnd] = useState(30);

  // Preview potongan URL
  const [preview, setPreview] = useState(null);
  const [previewLoading, setPreviewLoading] = useState(false);

  // Output
  const [resolution, setResolution] = useState("1080");
  const [aspect, setAspect] = useState("9:16");
  const [vbr, setVbr] = useState("2500k");
  const [abr, setAbr] = useState("128k");

  const [busy, setBusy] = useState(null); // 'clip' | 'trim' | 'preview' | null
  const [progress, setProgress] = useState(0);
  const [error, setError] = useState(null);

  const localVideoRef = useRef(null);

  const selectionValid = end > start && end - start >= 0.3;
  const busyAny = busy !== null || uploading || infoLoading || previewLoading;
  const hasSource = mode === "url" ? !!info : !!local;

  const resetTimeline = (total) => {
    const safeTotal = Number(total) || 30;
    setDuration(safeTotal);
    setStart(0);
    setEnd(Math.min(30, safeTotal));
    setPreview(null);
  };

  const handleFetchInfo = async () => {
    if (!url.trim()) return;
    setInfoLoading(true);
    setError(null);
    setInfo(null);
    setPreview(null);
    try {
      const { data } = await getMediaInfo(url.trim());
      setInfo(data);
      resetTimeline(data.duration || 30);
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
    } finally {
      setInfoLoading(false);
    }
  };

  const handleUpload = async (file) => {
    if (!file) return;
    setUploading(true);
    setUploadProgress(0);
    setError(null);
    setLocal(null);
    setPreview(null);
    try {
      const { data } = await uploadVideo(file, setUploadProgress);
      setLocal(data);
      resetTimeline(data.duration || 30);
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
    } finally {
      setUploading(false);
    }
  };

  const handlePreview = async () => {
    if (!info || !selectionValid) return;
    setPreviewLoading(true);
    setBusy("preview");
    setProgress(5);
    setError(null);
    setPreview(null);
    try {
      const res = await startPreview({
        url: url.trim(),
        start_time: sec(start),
        end_time: sec(end),
      });
      const jobId = res.data?.id;
      if (!jobId) throw new Error("Gagal memulai preview");
      const final = await waitForJob(jobId, {
        interval: 1500,
        onUpdate: (job) => setProgress(Math.max(5, job.progress || 0)),
      });
      if (final.status === "failed") throw new Error(final.error || "Preview gagal");
      const previewUrl = final.preview_url || final.result_url;
      if (!previewUrl) throw new Error("Preview tidak tersedia");
      setPreview({ url: previewUrl, duration: final.duration });
      setProgress(100);
    } catch (err) {
      setError(err.message);
    } finally {
      setPreviewLoading(false);
      setBusy(null);
    }
  };

  const handleClipUrl = async () => {
    if (!selectionValid) {
      setError("Waktu akhir harus lebih besar dari waktu awal (minimal 0,3 detik).");
      return;
    }
    setBusy("clip");
    setProgress(5);
    setError(null);
    try {
      const res = await startClip({
        url: url.trim(),
        start_time: sec(start),
        end_time: sec(end),
        resolution,
        aspect_ratio: aspect,
        video_bitrate: vbr,
        audio_bitrate: abr,
      });
      const jobId = res.data?.id || res.data?.job_id;
      if (!jobId) throw new Error("Gagal memulai proses potong");
      const final = await waitForJob(jobId, {
        interval: 1500,
        onUpdate: (job) => setProgress(Math.max(5, job.progress || 0)),
      });
      if (final.status === "failed") throw new Error(final.error || "Potong video gagal");
      const clipUrl = final.clip_url || final.result_url;
      if (!clipUrl) throw new Error("Hasil potongan tidak ditemukan");
      onStartEditing?.({ clip_url: clipUrl, job_id: jobId, duration: final.duration });
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(null);
    }
  };

  const handleTrimLocal = async () => {
    if (!local || !selectionValid) {
      setError("Waktu akhir harus lebih besar dari waktu awal (minimal 0,3 detik).");
      return;
    }
    setBusy("trim");
    setProgress(5);
    setError(null);
    try {
      const res = await trimLocal({
        video_url: local.video_url,
        start_time: sec(start),
        end_time: sec(end),
        resolution,
        aspect_ratio: aspect,
        video_bitrate: vbr,
        audio_bitrate: abr,
      });
      const jobId = res.data?.id || res.data?.job_id;
      if (!jobId) throw new Error("Gagal memulai proses potong");
      const final = await waitForJob(jobId, {
        interval: 1200,
        onUpdate: (job) => setProgress(Math.max(5, job.progress || 0)),
      });
      if (final.status === "failed") throw new Error(final.error || "Potong video gagal");
      const clipUrl = final.clip_url || final.result_url;
      if (!clipUrl) throw new Error("Hasil potongan tidak ditemukan");
      onStartEditing?.({ clip_url: clipUrl, job_id: jobId, duration: final.duration });
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(null);
    }
  };

  const handleEditUntrimmed = () => {
    if (!local) return;
    onStartEditing?.({
      clip_url: local.video_url,
      duration: local.duration,
      title: local.filename,
    });
  };

  const handlePlayRange = () => {
    const video = localVideoRef.current;
    if (!video) return;
    video.currentTime = start;
    video.play().catch(() => {});
  };

  const stopAtEnd = (e) => {
    if (e.target.currentTime >= end) {
      e.target.pause();
    }
  };

  return (
    <div className="glass-panel p-6 md:p-10 max-w-4xl mx-auto space-y-7">
      <div className="flex items-center gap-3">
        <div className="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center text-accent-400">
          <Film size={20} />
        </div>
        <div>
          <h1 className="text-2xl font-display font-bold">Input &amp; Clipping</h1>
          <p className="text-white/50 text-sm">
            Ambil video dari URL atau upload file lokal, cek preview, lalu potong dan edit.
          </p>
        </div>
      </div>

      {/* Tab sumber video */}
      <div className="flex gap-2">
        <button
          onClick={() => setMode("url")}
          className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm border transition ${
            mode === "url"
              ? "bg-accent-500/15 border-accent-500/40 text-accent-300"
              : "bg-white/5 border-white/10 text-white/60 hover:text-white"
          }`}
        >
          <Link2 size={16} /> URL Video
        </button>
        <button
          onClick={() => setMode("upload")}
          className={`flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm border transition ${
            mode === "upload"
              ? "bg-accent-500/15 border-accent-500/40 text-accent-300"
              : "bg-white/5 border-white/10 text-white/60 hover:text-white"
          }`}
        >
          <Upload size={16} /> Upload Video
        </button>
      </div>

      {mode === "url" && (
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-white/70 mb-2">
              URL Video (YouTube, TikTok, Instagram, dll.)
            </label>
            <div className="flex flex-col sm:flex-row gap-2">
              <Input
                placeholder="https://www.youtube.com/watch?v=..."
                value={url}
                onChange={(e) => {
                  setUrl(e.target.value);
                  if (info) setInfo(null);
                }}
                onKeyDown={(e) => {
                  if (e.key === "Enter") handleFetchInfo();
                }}
              />
              <Button
                variant="secondary"
                onClick={handleFetchInfo}
                disabled={!url.trim() || infoLoading}
                className="sm:w-40 flex items-center justify-center gap-2"
              >
                {infoLoading ? <Loader2 size={16} className="animate-spin" /> : <Info size={16} />}
                {infoLoading ? "Mengambil..." : "Ambil Info"}
              </Button>
            </div>
            <p className="text-[11px] text-white/35 mt-1.5">
              Metadata &amp; preview diambil dengan yt-dlp. Untuk YouTube, preview berupa
              potongan kualitas rendah (maks 480p / 5 menit).
            </p>
          </div>

          {info && (
            <div className="flex gap-4 rounded-xl bg-white/5 border border-white/10 p-3">
              {info.thumbnail ? (
                <img
                  src={info.thumbnail}
                  alt="thumbnail"
                  className="w-32 aspect-video object-cover rounded-lg border border-white/10"
                />
              ) : (
                <div className="w-32 aspect-video rounded-lg bg-white/5 border border-white/10 flex items-center justify-center text-white/30">
                  <Film size={22} />
                </div>
              )}
              <div className="min-w-0 flex-1">
                <h3 className="text-sm font-semibold text-white line-clamp-2">
                  {info.title || "Tanpa judul"}
                </h3>
                <p className="text-xs text-white/50 mt-0.5">
                  {info.uploader || "-"}
                  {info.duration ? ` • ${formatShort(info.duration)}` : ""}
                  {info.width && info.height ? ` • ${info.width}×${info.height}` : ""}
                </p>
                <div className="flex items-center gap-1.5 mt-2 text-[11px] text-green-400">
                  <CheckCircle2 size={12} /> Metadata siap. Atur rentang potongan di bawah.
                </div>
              </div>
            </div>
          )}
        </div>
      )}

      {mode === "upload" && (
        <div className="space-y-4">
          <div>
            <label className="block text-sm font-medium text-white/70 mb-2">
              Pilih file video dari komputer
            </label>
            <input
              type="file"
              accept="video/mp4,video/quicktime,video/x-matroska,video/webm,video/*"
              disabled={uploading}
              onChange={(e) => handleUpload(e.target.files?.[0])}
              className="w-full text-sm text-white/60 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-medium file:bg-accent-500/20 file:text-accent-300 hover:file:bg-accent-500/30 file:cursor-pointer bg-white/5 border border-white/10 rounded-xl p-2"
            />
            <p className="text-[11px] text-white/35 mt-1.5">
              MP4, MOV, MKV, WEBM, M4V • maksimal 500 MB
            </p>
          </div>

          {uploading && (
            <div className="space-y-2">
              <div className="flex justify-between text-xs text-white/60">
                <span className="flex items-center gap-1.5">
                  <Loader2 size={14} className="animate-spin" /> Mengunggah video...
                </span>
                <span className="text-accent-400">{uploadProgress}%</span>
              </div>
              <Progress value={uploadProgress} />
            </div>
          )}

          {local && (
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
              <div className="sm:col-span-1">
                <video
                  ref={localVideoRef}
                  src={mediaUrl(local.video_url)}
                  controls
                  preload="metadata"
                  onTimeUpdate={stopAtEnd}
                  className="w-full rounded-xl border border-white/10 bg-black aspect-[9/16] object-contain"
                />
              </div>
              <div className="sm:col-span-2 space-y-3">
                <div className="rounded-xl bg-white/5 border border-white/10 p-3">
                  <div className="flex items-center gap-2 text-sm text-white">
                    <FileVideo size={16} className="text-accent-400" />
                    <span className="truncate">{local.filename}</span>
                  </div>
                  <p className="text-xs text-white/50 mt-1">
                    {formatShort(local.duration)} • {local.width}×{local.height} •{" "}
                    {(local.size / (1024 * 1024)).toFixed(1)} MB
                  </p>
                </div>
                <button
                  onClick={handlePlayRange}
                  className="flex items-center gap-2 px-3 py-2 rounded-lg bg-white/5 border border-white/10 text-xs text-white/70 hover:text-white hover:bg-white/10"
                >
                  <Play size={14} /> Putar rentang terpilih ({formatShort(start)} –{" "}
                  {formatShort(end)})
                </button>
                <p className="text-[11px] text-white/35">
                  Tidak perlu memotong? Klik <b>Edit Tanpa Potong</b> untuk langsung masuk ke
                  editor.
                </p>
              </div>
            </div>
          )}
        </div>
      )}

      {/* Mode Clipping: Single Range vs Multi-Segment Chat */}
      <div className="border-t border-white/10 pt-5 space-y-4">
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <button
              type="button"
              onClick={() => setClipMethod("single")}
              className={`flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold border transition ${
                clipMethod === "single"
                  ? "bg-accent-500/15 border-accent-500/40 text-accent-300 shadow-sm"
                  : "bg-white/5 border-white/10 text-white/50 hover:text-white"
              }`}
            >
              <Scissors size={15} />
              1 Rentang Waktu (Single)
            </button>
            <button
              type="button"
              onClick={() => setClipMethod("multi")}
              className={`flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold border transition ${
                clipMethod === "multi"
                  ? "bg-accent-500/15 border-accent-500/40 text-accent-300 shadow-sm"
                  : "bg-white/5 border-white/10 text-white/50 hover:text-white"
              }`}
            >
              <MessageSquareText size={15} />
              Rangkuman Chat (Multi-Durasi)
            </button>
          </div>
        </div>

        {clipMethod === "single" && (
          <>
            {hasSource ? (
              <div className="space-y-4">
                <TrimTimeline
                  duration={duration}
                  start={start}
                  end={end}
                  disabled={busyAny}
                  onChange={(next) => {
                    setStart(next.start);
                    setEnd(next.end);
                    setPreview(null);
                  }}
                />

                <div className="flex flex-wrap gap-2">
                  {mode === "url" && (
                    <Button
                      variant="secondary"
                      onClick={handlePreview}
                      disabled={busyAny || !selectionValid}
                      className="flex items-center gap-2"
                    >
                      {previewLoading ? (
                        <Loader2 size={16} className="animate-spin" />
                      ) : (
                        <Eye size={16} />
                      )}
                      {previewLoading ? "Membuat preview..." : "Preview Potongan"}
                    </Button>
                  )}
                  <Button
                    variant="primary"
                    onClick={mode === "url" ? handleClipUrl : handleTrimLocal}
                    disabled={busyAny || !selectionValid}
                    className="flex items-center gap-2"
                  >
                    {busy === "clip" || busy === "trim" ? (
                      <Loader2 size={16} className="animate-spin" />
                    ) : (
                      <Scissors size={16} />
                    )}
                    {busy === "clip" || busy === "trim" ? "Memotong..." : "Potong & Edit"}
                  </Button>
                  {mode === "upload" && local && (
                    <Button
                      variant="ghost"
                      onClick={handleEditUntrimmed}
                      disabled={busyAny}
                      className="flex items-center gap-2"
                    >
                      <Wand2 size={16} /> Edit Tanpa Potong
                    </Button>
                  )}
                </div>

                {preview && (
                  <div className="rounded-xl bg-white/5 border border-white/10 p-3 space-y-2">
                    <div className="flex items-center justify-between">
                      <h3 className="text-sm font-semibold text-white flex items-center gap-2">
                        <Eye size={16} /> Preview Potongan
                      </h3>
                      <span className="text-[11px] text-white/40">
                        {preview.duration ? `${preview.duration.toFixed(1)} detik` : ""}
                      </span>
                    </div>
                    <video
                      key={preview.url}
                      src={mediaUrl(preview.url)}
                      controls
                      autoPlay
                      className="w-full max-h-[420px] rounded-lg border border-white/10 bg-black"
                    />
                    <p className="text-[11px] text-white/35">
                      Ini hanya preview kualitas rendah. Hasil akhir mengikuti pengaturan output di
                      bawah.
                    </p>
                  </div>
                )}
              </div>
            ) : (
              <div className="text-xs text-white/40 p-4 rounded-xl border border-dashed border-white/10 text-center">
                {mode === "url"
                  ? "Masukkan URL video di atas dan klik 'Ambil Info' untuk mengatur rentang potongan tunggal."
                  : "Pilih file video di atas untuk mengatur rentang potongan tunggal."}
              </div>
            )}
          </>
        )}

        {clipMethod === "multi" && (
          <MultiSegmentClipper
            sourceType={mode}
            url={url}
            local={local}
            resolution={resolution}
            aspect={aspect}
            vbr={vbr}
            abr={abr}
            onStartEditing={onStartEditing}
          />
        )}
      </div>

      <div className="border-t border-white/10 pt-5">
        <h3 className="text-sm font-semibold text-white/80 flex items-center gap-2 mb-3">
          <Layers size={16} /> Pengaturan Output
        </h3>
        <div className="grid grid-cols-2 md:grid-cols-4 gap-4">
          <Select
            label="Resolusi"
            options={RESOLUTION_OPTIONS.map((v) => ({ label: v + "p", value: v }))}
            value={resolution}
            onChange={setResolution}
          />
          <Select
            label="Aspek Rasio"
            options={ASPECT_RATIO_OPTIONS}
            value={aspect}
            onChange={setAspect}
          />
          <Select
            label="Bitrate Video"
            options={VIDEO_BITRATE_OPTIONS.map((v) => ({ label: v, value: v }))}
            value={vbr}
            onChange={setVbr}
          />
          <Select
            label="Bitrate Audio"
            options={AUDIO_BITRATE_OPTIONS.map((v) => ({ label: v, value: v }))}
            value={abr}
            onChange={setAbr}
          />
        </div>
      </div>

      {busy && (
        <div className="rounded-xl bg-white/5 border border-white/10 p-4">
          <div className="flex justify-between text-xs mb-2">
            <span className="text-white/60">
              {busy === "preview"
                ? "Mengunduh preview potongan..."
                : "Memotong video..."}
            </span>
            <span className="text-accent-400">{progress}%</span>
          </div>
          <Progress value={progress} />
        </div>
      )}

      {error && (
        <div className="rounded-xl bg-red-500/10 border border-red-500/30 p-4 text-red-400 text-sm whitespace-pre-wrap">
          {error}
        </div>
      )}

      <div className="text-[11px] text-white/30 flex items-center gap-2">
        <Download size={12} /> Setelah dipotong, Anda akan diarahkan ke AI Editor untuk caption,
        stiker, dan frame.
      </div>
    </div>
  );
};
