import api from "./axios";

export const extractCaption = (payload) =>
  api.post("/api/v1/caption/extract", payload);
