import api from "./axios";

export const applyEditor = (payload) => api.post("/api/v1/editor/apply", payload);
