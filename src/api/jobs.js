import api from "./axios";

export const getJob = (jobId) => api.get(`/api/v1/jobs/${jobId}`);

/**
 * Poll status job sampai selesai/gagal.
 * onUpdate dipanggil setiap kali status baru diterima (untuk progress bar).
 */
export const waitForJob = async (
  jobId,
  { interval = 1500, timeout = 20 * 60 * 1000, onUpdate } = {}
) => {
  const started = Date.now();
  while (Date.now() - started < timeout) {
    const { data } = await getJob(jobId);
    onUpdate?.(data);
    if (data.status === "completed" || data.status === "failed") {
      return data;
    }
    await new Promise((resolve) => setTimeout(resolve, interval));
  }
  throw new Error("Waktu tunggu proses habis. Cek kembali beberapa saat lagi.");
};
