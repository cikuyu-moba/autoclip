import { useState, useCallback } from "react";
import { extractCaption } from "@/api/caption";

export const useCaption = () => {
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);
  const [error, setError] = useState(null);

  const extract = useCallback(async (payload) => {
    setLoading(true);
    setError(null);
    try {
      const res = await extractCaption(payload);
      setResult(res.data);
      return res.data;
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  return { loading, result, error, extract };
};
