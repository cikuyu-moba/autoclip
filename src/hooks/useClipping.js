import { useState, useCallback } from "react";
import { startClip, getJobStatus } from "@/api/clipping";

export const useClipping = () => {
  const [loading, setLoading] = useState(false);
  const [job, setJob] = useState(null);
  const [error, setError] = useState(null);

  const clip = useCallback(async (payload) => {
    setLoading(true);
    setError(null);
    try {
      const res = await startClip(payload);
      setJob(res.data);
      return res.data;
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  const pollStatus = useCallback(async (jobId) => {
    try {
      const res = await getJobStatus(jobId);
      setJob(res.data);
      return res.data;
    } catch (err) {
      setError(err.message);
      return null;
    }
  }, []);

  return { loading, job, error, clip, pollStatus, setJob };
};
