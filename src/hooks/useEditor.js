import { useState, useCallback } from "react";
import { applyEditor } from "@/api/editor";
import { renderExport } from "@/api/export";

export const useEditor = () => {
  const [loading, setLoading] = useState(false);
  const [result, setResult] = useState(null);
  const [error, setError] = useState(null);

  const apply = useCallback(async (payload) => {
    setLoading(true);
    setError(null);
    try {
      const res = await applyEditor(payload);
      setResult(res.data);
      return res.data;
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  const render = useCallback(async (payload) => {
    setLoading(true);
    setError(null);
    try {
      const res = await renderExport(payload);
      setResult(res.data);
      return res.data;
    } catch (err) {
      setError(err.response?.data?.detail || err.message);
      return null;
    } finally {
      setLoading(false);
    }
  }, []);

  return { loading, result, error, apply, render };
};
