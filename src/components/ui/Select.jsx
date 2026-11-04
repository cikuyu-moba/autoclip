export const Select = ({ options, value, onChange, label, className = "" }) => {
  return (
    <div className="space-y-1.5">
      {label && <label className="text-sm text-white/70 font-medium">{label}</label>}
      <select
        className="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-white focus:outline-none focus:ring-2 focus:ring-accent-500"
        value={value}
        onChange={(e) => onChange(e.target.value)}
      >
        {options.map((o) => (
          <option key={o.value} value={o.value}>
            {o.label}
          </option>
        ))}
      </select>
    </div>
  );
};
