import { useEffect } from "react";
import { X } from "lucide-react";

export const Modal = ({ isOpen, onClose, title, children }) => {
  useEffect(() => {
    if (!isOpen) return;
    const esc = (e) => e.key === "Escape" && onClose();
    window.addEventListener("keydown", esc);
    return () => window.removeEventListener("keydown", esc);
  }, [isOpen, onClose]);

  if (!isOpen) return null;

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm" onClick={onClose}>
      <div className="bg-dark-900 border border-white/10 rounded-2xl p-6 max-w-2xl w-full mx-4 shadow-2xl" onClick={(e) => e.stopPropagation()}>
        {title && (
          <div className="flex items-center gap-2 mb-4">
            <X className="w-5 h-5 cursor-pointer text-white/40 hover:text-white" onClick={onClose} />
            <h2 className="flex-1 font-display text-xl font-bold">{title}</h2>
          </div>
        )}
        {children}
      </div>
    </div>
  );
};
