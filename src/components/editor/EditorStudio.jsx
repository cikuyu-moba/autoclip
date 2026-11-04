import { useState, useEffect } from "react";
import { useCaption } from "@/hooks/useCaption";
import { useEditor } from "@/hooks/useEditor";
import { waitForJob } from "@/api/jobs";
import { mediaUrl } from "@/utils/media";
import { VideoPreview } from "@/components/editor/VideoPreview";
import { CaptionTrack } from "@/components/editor/CaptionTrack";
import { SubtitleStyler } from "@/components/editor/SubtitleStyler";
import { StickerPanel } from "@/components/editor/StickerPanel";
import { FramePanel } from "@/components/editor/FramePanel";
import { ItemTimeline } from "@/components/editor/ItemTimeline";
import { VariationPanel, DEFAULT_VARIATION } from "@/components/editor/VariationPanel";
import { ImportVideoModal } from "@/components/editor/ImportVideoModal";
import { Button, Progress } from "@/components/ui";
import { ArrowLeft, Wand2, Download, Upload, Sticker, Frame, Film } from "lucide-react";

const DEFAULT_STYLE = {
  font_family: "Arial Black",
  font_size: 72,
  primary_color: "&H00FFFFFF",
  outline_color: "&H00000000",
  outline_width: 3,
  shadow: 1.5,
  alignment: 2,
  margin_v: 40,
  pop_up_effect: true,
  highlight_color: "&H0000FFFF",
};

export const EditorStudio = ({ initialClip, onBack }) => {
  const [videoUrl, setVideoUrl] = useState(initialClip?.clip_url || "");
  const [videoTitle, setVideoTitle] = useState(initialClip?.title || "");
  const [duration, setDuration] = useState(initialClip?.duration || 0);
  const [currentTime, setCurrentTime] = useState(0);
  const [isPlaying, setIsPlaying] = useState(false);
  const [captions, setCaptions] = useState([]);
  const [style, setStyle] = useState(DEFAULT_STYLE);
  const [stickers, setStickers] = useState([]);
  const [frames, setFrames] = useState([]);
  const [selectedSticker, setSelectedSticker] = useState(null);
  const [selectedFrame, setSelectedFrame] = useState(null);
  const [importOpen, setImportOpen] = useState(false);
  const [busy, setBusy] = useState(null); // 'caption' | 'render' | null
  const [progress, setProgress] = useState(0);
  const [language, setLanguage] = useState("auto");
  const [exportResult, setExportResult] = useState(null);
  const [variation, setVariation] = useState(DEFAULT_VARIATION);
  const [error, setError] = useState(null);

  const { extract } = useCaption();
  const { apply } = useEditor();

  useEffect(() => {
    if (initialClip?.clip_url) {
      setVideoUrl(initialClip.clip_url);
      setVideoTitle(initialClip.title || "");
      if (initialClip.duration) setDuration(initialClip.duration);
    }
  }, [initialClip]);

  const updateStyle = (patch) => setStyle((prev) => ({ ...prev, ...patch }));

  const handleImported = (data) => {
    setVideoUrl(data.video_url || data.clip_url || "");
    setVideoTitle(data.filename || data.title || "");
    setDuration(data.duration || 0);
    setCurrentTime(0);
    setIsPlaying(false);
    setCaptions([]);
    setStickers([]);
    setFrames([]);
    setSelectedSticker(null);
    setSelectedFrame(null);
    setExportResult(null);
    setError(null);
  };

  const handleExtract = async () => {
    if (!videoUrl) return;
    setError(null);
    setBusy("caption");
    setProgress(10);
    try {
      const res = await extract({
        video_url: videoUrl,
        language: language === "auto" ? null : language,
      });
      const jobId = res?.job_id;
      if (!jobId) throw new Error("Gagal memulai transkripsi");
      const final = await waitForJob(jobId, {
        interval: 2000,
        onUpdate: (job) => setProgress(Math.max(10, job.progress || 0)),
      });
      if (final.status === "failed") throw new Error(final.error || "Transkripsi gagal");
      setCaptions(final.segments || []);
      setProgress(100);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(null);
    }
  };

  const handleApply = async () => {
    if (!videoUrl) return;
    setError(null);
    setExportResult(null);
    setBusy("render");
    setProgress(5);
    try {
      const res = await apply({
        video_url: videoUrl,
        captions,
        style,
        stickers,
        frames,
        variation,
        resolution: "1080",
        aspect_ratio: "9:16",
        video_bitrate: "2500k",
        audio_bitrate: "128k",
      });
      const jobId = res?.job_id;
      if (!jobId) throw new Error("Gagal memulai render");
      const final = await waitForJob(jobId, {
        interval: 2000,
        onUpdate: (job) => setProgress(Math.max(5, job.progress || 0)),
      });
      if (final.status === "failed") throw new Error(final.error || "Render gagal");
      setExportResult(final);
      setProgress(100);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(null);
    }
  };

  const hasOverlay = stickers.length > 0 || frames.length > 0;
  const canRender = captions.length > 0 || hasOverlay;

  return (
    <div className="glass-panel p-6 md:p-8 max-w-6xl mx-auto space-y-6">
      <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div className="min-w-0">
          <h1 className="text-2xl font-display font-bold">AI Editor Studio</h1>
          <p className="text-white/50 text-sm truncate">
            {videoTitle ? (
              <>Editing: <span className="text-white/70">{videoTitle}</span></>
            ) : (
              "Import video, tambahkan caption, stiker, dan frame sebelum export."
            )}
          </p>
        </div>
        <div className="flex flex-wrap gap-2 items-center">
          <Button variant="secondary" onClick={() => setImportOpen(true)} disabled={busy !== null}>
            <Upload size={18} /> Import Video
          </Button>
          <select
            value={language}
            onChange={(e) => setLanguage(e.target.value)}
            className="bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-accent-500"
            title="Bahasa audio"
          >
            <option value="auto">Auto</option>
            <option value="id">Indonesia</option>
            <option value="en">English</option>
          </select>
          <Button variant="ghost" onClick={onBack}>
            <ArrowLeft size={18} /> Kembali
          </Button>
          <Button
            variant="primary"
            disabled={!videoUrl || busy !== null}
            onClick={handleExtract}
          >
            {busy === "caption" ? "Mentranskripsi..." : <><Wand2 size={18} /> Auto-Caption</>}
          </Button>
        </div>
      </div>

      {busy && (
        <div className="rounded-xl bg-white/5 border border-white/10 p-4">
          <div className="flex justify-between text-sm mb-2">
            <span className="text-white/70">
              {busy === "caption" ? "Transkripsi Whisper" : "Merender video"}
            </span>
            <span className="text-accent-400">{progress}%</span>
          </div>
          <Progress value={progress} size="lg" />
        </div>
      )}

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6">
        {/* Panel kiri: Preview + Caption track */}
        <div className="lg:col-span-7 space-y-4">
          {videoUrl ? (
            <VideoPreview
              src={mediaUrl(videoUrl)}
              duration={duration}
              currentTime={currentTime}
              isPlaying={isPlaying}
              stickers={stickers}
              frames={frames}
              onToggle={(next) =>
                setIsPlaying((prev) => (typeof next === "boolean" ? next : !prev))
              }
              onDurationChange={(value) => {
                if (value) setDuration(value);
              }}
              onTimeUpdate={setCurrentTime}
              captions={captions}
            />
          ) : (
            <div className="w-full aspect-[9/16] rounded-2xl border border-dashed border-white/15 bg-white/[0.02] flex flex-col items-center justify-center gap-3 text-center p-6">
              <Film size={40} className="text-white/20" />
              <p className="text-sm text-white/50">Belum ada video di editor.</p>
              <Button variant="primary" onClick={() => setImportOpen(true)}>
                <Upload size={18} /> Import Video
              </Button>
              <p className="text-[11px] text-white/30">
                Upload file baru atau pilih video yang sudah pernah diproses.
              </p>
            </div>
          )}
          <CaptionTrack
            captions={captions}
            currentTime={currentTime}
            onChange={setCaptions}
          />
        </div>

        {/* Panel kanan: Pengaturan editor */}
        <div className="lg:col-span-5 space-y-5">
          <SubtitleStyler style={style} onChange={updateStyle} />

          <div className="border-t border-white/10 pt-4 space-y-3">
            <StickerPanel
              stickers={stickers}
              onChange={setStickers}
              selectedId={selectedSticker}
              onSelect={setSelectedSticker}
            />
            <ItemTimeline
              title="Timeline Stiker"
              icon={<Sticker size={13} />}
              items={stickers}
              duration={duration}
              selectedId={selectedSticker}
              onSelect={setSelectedSticker}
              onChange={setStickers}
              emptyText="Tambah stiker untuk mengatur waktu & posisinya."
            />
          </div>

          <div className="border-t border-white/10 pt-4 space-y-3">
            <FramePanel
              frames={frames}
              onChange={setFrames}
              selectedId={selectedFrame}
              onSelect={setSelectedFrame}
            />
            <ItemTimeline
              title="Timeline Frame"
              icon={<Frame size={13} />}
              items={frames}
              duration={duration}
              selectedId={selectedFrame}
              onSelect={setSelectedFrame}
              onChange={setFrames}
              positionControls={false}
              emptyText="Generate frame untuk mengatur kapan bingkai tampil."
            />
          </div>

          <VariationPanel variation={variation} onChange={setVariation} />

          <div className="pt-1">
            <Button
              variant="primary"
              disabled={busy !== null || !videoUrl || !canRender}
              onClick={handleApply}
              className="w-full py-4 text-lg flex items-center justify-center gap-2"
            >
              {busy === "render" ? "Merender..." : <><Wand2 size={20} /> Terapkan &amp; Pratinjau</>}
            </Button>
            {!canRender && videoUrl && (
              <p className="text-[11px] text-white/35 text-center mt-2">
                Tambahkan caption, stiker, atau frame dulu untuk merender.
              </p>
            )}
          </div>
        </div>
      </div>

      {error && (
        <div className="rounded-xl bg-red-500/10 border border-red-500/30 p-4 text-red-400 text-sm whitespace-pre-wrap">
          {error}
        </div>
      )}

      {exportResult?.download_url && (
        <div className="rounded-xl bg-green-500/10 border border-green-500/30 p-4 space-y-3">
          <h3 className="text-green-400 font-semibold">Export Selesai</h3>
          {(exportResult.variation?.audio || exportResult.variation?.video) && (
            <p className="text-xs text-white/50">
              Variasi diterapkan
              {exportResult.variation.audio
                ? ` · pitch ${exportResult.variation.audio.pitch_percent > 0 ? "+" : ""}${exportResult.variation.audio.pitch_percent}%`
                : ""}
              {exportResult.variation.video
                ? ` · hue ${exportResult.variation.video.hue_deg > 0 ? "+" : ""}${exportResult.variation.video.hue_deg}°`
                : ""}
            </p>
          )}
          <video
            src={mediaUrl(exportResult.download_url)}
            controls
            className="w-full max-w-xs rounded-lg border border-white/10 bg-black"
          />
          <a
            href={mediaUrl(exportResult.download_url)}
            target="_blank"
            rel="noopener noreferrer"
            download
            className="flex items-center gap-2 text-accent-400 hover:underline"
          >
            <Download size={18} /> Unduh Video Hasil Edit
          </a>
        </div>
      )}

      <ImportVideoModal
        isOpen={importOpen}
        onClose={() => setImportOpen(false)}
        onImported={handleImported}
      />
    </div>
  );
};
