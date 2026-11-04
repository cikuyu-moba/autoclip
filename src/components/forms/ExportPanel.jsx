import { useState } from "react";
import { renderExport } from "@/api/export";
import { waitForJob } from "@/api/jobs";
import { mediaUrl } from "@/utils/media";
import {
  RESOLUTION_OPTIONS,
  ASPECT_RATIO_OPTIONS,
  VIDEO_BITRATE_OPTIONS,
  AUDIO_BITRATE_OPTIONS,
} from "@/utils/ffmpegHelpers";
import { Button, Progress, Select } from "@/components/ui";
import { VariationPanel, DEFAULT_VARIATION } from "@/components/editor/VariationPanel";
import { Download, FileVideo } from "lucide-react";

export const ExportPanel = ({ clip }) => {
  const [resolution, setResolution] = useState("1080");
  const [aspect, setAspect] = useState("9:16");
  const [vbr, setVbr] = useState("2500k");
  const [abr, setAbr] = useState("128k");
  const [variation, setVariation] = useState(DEFAULT_VARIATION);
  const [busy, setBusy] = useState(false);
  const [progress, setProgress] = useState(0);
  const [result, setResult] = useState(null);
  const [error, setError] = useState(null);

  const handleRender = async () => {
    if (!clip?.clip_url) {
      setError("Belum ada video. Potong video dulu di tab Clip.");
      return;
    }
    setBusy(true);
    setError(null);
    setResult(null);
    setProgress(5);
    try {
      const res = await renderExport({
        video_url: clip.clip_url,
        resolution,
        aspect_ratio: aspect,
        video_bitrate: vbr,
        audio_bitrate: abr,
        variation,
      });
      const final = await waitForJob(res.data.job_id, {
        interval: 2000,
        onUpdate: (job) => setProgress(Math.max(5, job.progress || 0)),
      });
      if (final.status === "failed") throw new Error(final.error || "Render gagal");
      setResult(final);
      setProgress(100);
    } catch (err) {
      setError(err.message);
    } finally {
      setBusy(false);
    }
  };

  return (
    <div className="glass-panel p-8 md:p-12 max-w-3xl mx-auto space-y-6">
      <div className="flex items-center gap-3">
        <div className="w-10 h-10 bg-accent-500/20 rounded-xl flex items-center justify-center text-accent-400">
          <FileVideo size={20} />
        </div>
        <div>
          <h1 className="text-2xl font-display font-bold">Export Video</h1>
          <p className="text-white/50 text-sm">
            Render ulang hasil potongan ke resolusi &amp; aspek rasio pilihan Anda.
          </p>
        </div>
      </div>

      {clip?.clip_url ? (
        <video
          src={mediaUrl(clip.clip_url)}
          controls
          className="w-full max-w-xs rounded-lg border border-white/10 bg-black"
        />
      ) : (
        <div className="rounded-xl bg-white/5 border border-white/10 p-4 text-sm text-white/50">
          Belum ada video. Kembali ke tab Clip dan potong video terlebih dahulu.
        </div>
      )}

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

      <VariationPanel variation={variation} onChange={setVariation} />

      {busy && (
        <div className="rounded-xl bg-white/5 border border-white/10 p-4">
          <div className="flex justify-between text-sm mb-2">
            <span className="text-white/70">Merender video</span>
            <span className="text-accent-400">{progress}%</span>
          </div>
          <Progress value={progress} size="lg" />
        </div>
      )}

      {error && (
        <div className="rounded-xl bg-red-500/10 border border-red-500/30 p-4 text-red-400 text-sm">
          {error}
        </div>
      )}

      <Button
        variant="primary"
        disabled={busy || !clip?.clip_url}
        onClick={handleRender}
        className="w-full py-4 text-lg flex items-center justify-center gap-2"
      >
        {busy ? "Merender..." : <><Download size={20} /> Render &amp; Download</>}
      </Button>

      {result?.download_url && (
        <div className="rounded-xl bg-green-500/10 border border-green-500/30 p-4 space-y-3">
          <h3 className="text-green-400 font-semibold">Export Selesai</h3>
          {(result.variation?.audio || result.variation?.video) && (
            <p className="text-xs text-white/50">
              Variasi diterapkan
              {result.variation.audio
                ? ` · pitch ${result.variation.audio.pitch_percent > 0 ? "+" : ""}${result.variation.audio.pitch_percent}%`
                : ""}
              {result.variation.video
                ? ` · hue ${result.variation.video.hue_deg > 0 ? "+" : ""}${result.variation.video.hue_deg}°`
                : ""}
            </p>
          )}
          <video
            src={mediaUrl(result.download_url)}
            controls
            className="w-full max-w-xs rounded-lg border border-white/10 bg-black"
          />
          <a
            href={mediaUrl(result.download_url)}
            download
            className="flex items-center gap-2 text-accent-400 hover:underline"
          >
            <Download size={18} /> Unduh video hasil export
          </a>
        </div>
      )}
    </div>
  );
};
