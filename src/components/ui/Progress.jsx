import { cn } from "@/utils/cn";

export const Progress = ({ value = 0, size = "md", className = "" }) => {
  const sizes = { sm: "h-1.5", md: "h-3", lg: "h-5" };
  return (
    <div className={cn("w-full rounded-full bg-white/10 overflow-hidden", sizes[size], className)}>
      <div
        className="bg-accent-500 h-full transition-all duration-300"
        style={{ width: `${Math.max(0, Math.min(100, value))}%` }}
      />
    </div>
  );
};
