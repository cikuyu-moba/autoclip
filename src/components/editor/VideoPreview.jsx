import { useRef } from "react";
import { Play, Pause } from "lucide-react";
import { formatShort } from "@/utils/formatTime";

const isActive = (item, time) => {
  const start = Number(item.start_time) || 0;
  const end = item.end_time == null ? Infinity : Number(item.end_time);
  return time >= start && time <= end;
};

export const VideoPreview = ({
  src,
  duration = 0,
  captions = [],
  stickers = [],
  frames = [],
  currentTime,
  isPlaying,
  onToggle,
  onDurationChange,
  onTimeUpdate,
}) => {
  const videoRef = useRef(null);

  const activeCaption =
    captions.find((c) => currentTime >= c.start && currentTime <= c.end) || null;

  const activeFrames = frames.filter((f) => isActive(f, currentTime));
  const activeStickers = stickers.filter((s) => isActive(s, currentTime));

  const togglePlayback = () => {
    const video = videoRef.current;
    if (!video) {
      onToggle();
      return;
    }
    if (video.paused) {
      video.play().catch(() => {});
    } else {
      video.pause();
    }
  };

  return (
    <div className="relative w-full aspect-[9/16] bg-black rounded-2xl overflow-hidden shadow-2xl border border-white/10">
      <video
        ref={videoRef}
        className="absolute inset-0 w-full h-full object-cover"
        src={src || ""}
        preload="metadata"
        controls
        onLoadedMetadata={(e) => onDurationChange(e.target.duration || 0)}
        onTimeUpdate={(e) => onTimeUpdate(e.target.currentTime)}
        onPlay={() => onToggle(true)}
        onPause={() => onToggle(false)}
      />

      {/* Frame / overlay penuh layar */}
      {activeFrames.map((frame) => (
        <img
          key={frame.id}
          src={frame.src}
          alt=""
          className="absolute inset-0 w-full h-full object-cover pointer-events-none"
          style={{ zIndex: 1 }}
        />
      ))}

      {/* Stiker dengan posisi & rotasi */}
      {activeStickers.map((sticker) => (
        <img
          key={sticker.id}
          src={sticker.src}
          alt=""
          className="absolute pointer-events-none"
          style={{
            zIndex: 2,
            left: `${(sticker.x ?? 0.5) * 100}%`,
            top: `${(sticker.y ?? 0.5) * 100}%`,
            width: `${(sticker.width ?? 0.2) * 100}%`,
            transform: `translate(-50%, -50%) rotate(${sticker.rotate ?? 0}deg)`,
          }}
        />
      ))}

      {/* Overlay caption aktif (visualisasi pop-up gaya Reels/TikTok) */}
      {activeCaption && (
        <div
          className="absolute bottom-[20%] left-1/2 -translate-x-1/2 pointer-events-none px-4 w-full text-center"
          style={{ zIndex: 10, animation: "popUp 0.25s cubic-bezier(0.34, 1.56, 0.64, 1)" }}
        >
          <div
            className="inline-block px-5 py-2.5 rounded-2xl bg-black/70 backdrop-blur-md text-white font-bold text-2xl shadow-2xl border border-white/20 max-w-full"
            style={{
              fontFamily: '"Arial Black", sans-serif',
              color: "#fff",
              textShadow: "2px 2px 0 #000",
            }}
          >
            {activeCaption.text}
          </div>
        </div>
      )}

      <div
        className="absolute inset-x-0 bottom-0 flex items-end justify-between p-3 bg-gradient-to-t from-black/60 via-transparent to-transparent pointer-events-none"
        style={{ zIndex: 20 }}
      >
        <div className="flex items-center gap-2 text-sm text-white">
          <span>{formatShort(currentTime || 0)}</span>
          <span className="text-white/40">/</span>
          <span>{formatShort(duration || 0)}</span>
        </div>
        <button
          onClick={togglePlayback}
          className="w-10 h-10 rounded-full bg-accent-500 flex items-center justify-center shadow-lg hover:bg-accent-600 pointer-events-auto"
          aria-label={isPlaying ? "Pause" : "Play"}
        >
          {isPlaying ? <Pause size={18} strokeWidth={2.5} /> : <Play size={18} strokeWidth={2.5} />}
        </button>
      </div>
    </div>
  );
};
