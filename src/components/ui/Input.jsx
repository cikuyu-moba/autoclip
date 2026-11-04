export const Input = ({ className = "", ...props }) => {
  return (
    <input
      className={
        "w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white placeholder-white/30 " +
        "focus:outline-none focus:ring-2 focus:ring-accent-500 focus:border-transparent transition-all " +
        className
      }
      {...props}
    />
  );
};
