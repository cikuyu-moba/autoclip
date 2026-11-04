import api from "./axios";

/** Cek provider AI gambar yang aktif (openai / gemini / openrouter). */
export const getAIStatus = () => api.get("/api/v1/ai/status");

/** Generate stiker atau frame dengan AI. */
export const generateAIImage = (payload) =>
  api.post("/api/v1/ai/image", payload, { timeout: 180000 });
