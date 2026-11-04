import { create } from "zustand";

// Fallback jika zustand belum terpasang: gunakan object sederhana
let store;
try {
  const { create: zustandCreate } = require("zustand");
  store = zustandCreate((set) => ({
    clip: null,
    captions: [],
    style: {
      font_family: "Arial Black",
      font_size: 48,
      primary_color: "&H00FFFFFF",
      outline_color: "&H00000000",
      outline_width: 3,
      shadow: 1.5,
      alignment: 2,
      margin_v: 40,
      pop_up_effect: true,
      highlight_color: "&H0000FFFF",
    },
    stickers: [],
    resolution: "1080",
    aspect_ratio: "9:16",
    video_bitrate: "2500k",
    audio_bitrate: "128k",
    setClip: (clip) => set({ clip }),
    setCaptions: (captions) => set({ captions }),
    setStyle: (style) => set({ style }),
    setStickers: (stickers) => set({ stickers }),
    setSetting: (key, value) => set((s) => ({ [key]: value })),
    addSticker: (sticker) => set((s) => ({ stickers: [...s.stickers, sticker] }),
    removeSticker: (id) => set((s) => ({ stickers: s.stickers.filter((st) => st.id !== id) }),
    reset: () => set({
      clip: null,
      captions: [],
      stickers: [],
      resolution: "1080",
      aspect_ratio: "9:16",
    }),
  }));
} catch {
  store = {
    clip: null,
    captions: [],
    stickers: [],
    resolution: "1080",
    aspect_ratio: "9:16",
    video_bitrate: "2500k",
    audio_bitrate: "128k",
    subscribe: () => () => {},
  };
}

export default store;
