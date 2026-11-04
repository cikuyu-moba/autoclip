import { Film, Wand2, Download } from "lucide-react";

export const Layout = ({ children, active = "clip", onNavigate }) => {
  const items = [
    { id: "clip", label: "Clip", icon: <Film size={18} /> },
    { id: "editor", label: "AI Editor", icon: <Wand2 size={18} /> },
    { id: "export", label: "Export", icon: <Download size={18} /> },
  ];

  return (
    <div className="min-h-screen bg-dark-950 text-white flex flex-col">
      {/* Navbar */}
      <nav className="border-b border-white/5 bg-dark-900/80 backdrop-blur sticky top-0 z-40">
        <div className="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
          <button
            onClick={() => onNavigate?.("clip")}
            className="flex items-center gap-3"
          >
            <div className="w-8 h-8 bg-gradient-to-br from-accent-400 to-accent-600 rounded-lg flex items-center justify-center font-bold text-dark-900 text-sm">AC</div>
            <span className="font-display font-bold text-xl tracking-tight">AutoClip</span>
          </button>
          <div className="flex items-center gap-2">
            {items.map((item) => (
              <NavItem
                key={item.id}
                icon={item.icon}
                label={item.label}
                active={active === item.id}
                onClick={() => onNavigate?.(item.id)}
              />
            ))}
          </div>
        </div>
      </nav>

      <main className="max-w-7xl w-full mx-auto px-4 py-8 flex-1">
        {children}
      </main>

      <footer className="border-t border-white/5 bg-dark-900 py-6">
        <div className="max-w-7xl mx-auto px-4 text-center text-xs text-white/40">
          AutoClip AI Video Clipper & Editor &bull; yt-dlp + FFmpeg + Whisper
        </div>
      </footer>
    </div>
  );
};

const NavItem = ({ icon, label, active, onClick }) => {
  return (
    <button
      onClick={onClick}
      className={`flex items-center gap-2 px-3 py-2 rounded-xl text-sm font-medium transition ${
        active
          ? "bg-accent-500/10 text-accent-400 border border-accent-500/30"
          : "text-white/40 hover:text-white hover:bg-white/5"
      }`}
    >
      {icon} {label}
    </button>
  );
};
