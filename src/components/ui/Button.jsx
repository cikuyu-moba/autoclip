import { cn } from "@/utils/cn";

export const Button = ({ className = "", variant = "primary", disabled = false, children, ...props }) => {
  const base =
    "inline-flex items-center justify-center rounded-xl font-medium transition-all duration-200 disabled:opacity-50 disabled:pointer-events-none";
  const variants = {
    primary: "bg-accent-500 text-white hover:bg-accent-600 active:scale-95",
    secondary: "bg-white/10 text-white hover:bg-white/15 border border-white/10",
    ghost: "bg-white/5 text-white hover:bg-white/10 border border-white/10",
    danger: "bg-red-500 text-white hover:bg-red-600",
  };
  return (
    <button className={cn(base, variants[variant], className)} disabled={disabled} {...props}>
      {children}
    </button>
  );
};
