import api from "./axios";

export const startClip = (payload) => api.post("/api/v1/clipping/clip", payload);
export const startMultiClip = (payload) => api.post("/api/v1/clipping/multi-clip", payload);
export const parseTimestamps = (text) => api.post("/api/v1/clipping/parse-timestamps", { text });
export const getJobStatus = (jobId) => api.get(`/api/v1/jobs/${jobId}`);

