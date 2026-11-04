import { useEffect, useState } from "react";
import { Sparkles, Wand2, KeyRound, Loader2 } from "lucide-react";
import { Button, Modal } from "@/components/ui";
import { getAIStatus, generateAIImage } from "@/api/ai";

const TEMPLATES = {
  sticker: [
    "mahkota emas berkilau",
    "api oranye kartun",
    "hati merah dengan garis tepi putih",
    "bintang kuning lucu",
    "panah merah besar",
    "emoticon kaget",
  ],
  frame: [
    "bingkai neon ungu futuristik",
    "bingkai emas mewah dengan ornamen",
    "bingkai daun tropis hijau",
    "bingkai glitter pink",
    "bingkai api oranye di tepi layar",
  ],
};

/**
 * Tombol + modal untuk generate stiker/frame via AI.
 * onCreated menerima objek { src, provider, prompt }.
 */
export const AIImageGenerator = ({ kind = "sticker", onCreated, label }) => {
  const [open, setOpen] = useState(false);
  const [status, setStatus] = useState(null);
  const [prompt, setPrompt] = useState("");
  const [provider, setProvider] = useState("");
  const [busy, setBusy] = useState(false);
  const [error, setError] = useState(null);

  useEffect(() => {
    if (!open) return;
    setError(null);
    getAIStatus()
      .then((res) => setStatus(res.data))
      .catch(() => setStatus(null));
  }, [open]);

  const providers = status?.providers || {};
  const available = Object.entries(providers)
    .filter(([, ok]) => ok)
    .map(([name]) => name);
  const hasKey = available.length > 0;

  const handleGenerate = async () => {
    if (!prompt.trim()) return;
    setBusy(true);
    setError(null);
    try {
      const { data } = await generateAIImage({
        prompt: prompt.trim(),
        kind,
        provider: provider || null,
        transparent: kind === "sticker",
      });
      onCreated?.({ src: data.image_url, provider: data.provider, prompt: data.prompt });
      setPrompt("");
      setOpen(false);
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
    } finally {
      setBusy(false);
    }
  };

  return (
    <>
      <button
        type="button"
        onClick={() => setOpen(true)}
        className="flex items-center gap-1.5 px-3 py-1.5 bg-accent-500/20 border border-accent-500/40 rounded-lg text-xs text-accent-300 hover:bg-accent-500/30 transition"
      >
        <Sparkles size={14} /> {label || `Generate ${kind === "frame" ? "Frame" : "Stiker"} AI`}
      </button>

      <Modal isOpen={open} onClose={() => !busy && setOpen(false)} title={`Generate ${kind === "frame" ? "Frame" : "Stiker"} dengan AI`}>
        <div className="space-y-4 max-h-[70vh] overflow-y-auto pr-1">
          <p className="text-sm text-white/50">
            {kind === "frame"
              ? "Frame adalah overlay penuh layar. Bagian tengah dibuat transparan agar video tetap terlihat."
              : "Stiker dibuat dengan latar transparan sehingga menyatu dengan video."}
          </p>

          {status && !hasKey && (
            <div className="rounded-xl bg-amber-500/10 border border-amber-500/30 p-4 text-sm text-amber-300 space-y-2">
              <div className="flex items-center gap-2 font-semibold">
                <KeyRound size={16} /> API key AI belum diatur
              </div>
              <p className="text-amber-200/80">
                Tambahkan salah satu baris berikut di <code className="bg-black/30 px-1 rounded">backend/.env</code>,
                lalu restart backend:
              </p>
              <pre className="bg-black/40 rounded-lg p-2 text-[11px] overflow-x-auto text-amber-100">{`OPENAI_API_KEY=sk-...\n# atau\nGEMINI_API_KEY=...\n# atau\nOPENROUTER_API_KEY=sk-or-v1-...`}</pre>
              <p className="text-[11px] text-amber-200/60">
                Provider default bisa dipilih lewat <code>IMAGE_PROVIDER=openai</code>, <code>gemini</code>, atau <code>openrouter</code>.
              </p>
            </div>
          )}

          {hasKey && (
            <div>
              <label className="block text-xs text-white/60 mb-1">Provider</label>
              <select
                value={provider}
                onChange={(e) => setProvider(e.target.value)}
                className="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white focus:outline-none focus:ring-2 focus:ring-accent-500"
              >
                <option value="">Otomatis ({status?.default || "openai"})</option>
                {available.map((name) => (
                  <option key={name} value={name}>{name}</option>
                ))}
              </select>
            </div>
          )}

          <div>
            <label className="block text-xs text-white/60 mb-1">Deskripsi gambar</label>
            <textarea
              value={prompt}
              onChange={(e) => setPrompt(e.target.value)}
              rows={3}
              placeholder={kind === "frame" ? "contoh: bingkai neon biru futuristik" : "contoh: mahkota emas berkilau"}
              className="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-sm text-white placeholder:text-white/30 focus:outline-none focus:ring-2 focus:ring-accent-500"
            />
          </div>

          <div className="flex flex-wrap gap-2">
            {(TEMPLATES[kind === "frame" ? "frame" : "sticker"] || []).map((tpl) => (
              <button
                key={tpl}
                type="button"
                onClick={() => setPrompt(tpl)}
                className="px-2.5 py-1 rounded-lg text-[11px] bg-white/5 border border-white/10 text-white/60 hover:text-white hover:bg-white/10"
              >
                {tpl}
              </button>
            ))}
          </div>

          {error && (
            <div className="rounded-xl bg-red-500/10 border border-red-500/30 p-3 text-xs text-red-400 whitespace-pre-wrap">
              {error}
            </div>
          )}

          <Button
            variant="primary"
            disabled={busy || !prompt.trim() || !hasKey}
            onClick={handleGenerate}
            className="w-full py-3 flex items-center justify-center gap-2"
          >
            {busy ? (
              <><Loader2 size={18} className="animate-spin" /> Membuat gambar...</>
            ) : (
              <><Wand2 size={18} /> Generate</>
            )}
          </Button>
          {busy && (
            <p className="text-[11px] text-center text-white/40">
              Proses AI bisa memakan waktu 10–60 detik.
            </p>
          )}
        </div>
      </Modal>
    </>
  );
};
