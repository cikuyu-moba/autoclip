import api from "./axios";

export const renderExport = (payload) => api.post("/api/v1/export/render", payload);
