import { useState } from "react";
import { Layout } from "@/components/Layout";
import { ClipForm } from "@/components/forms/ClipForm";
import { ExportPanel } from "@/components/forms/ExportPanel";
import { EditorStudio } from "@/components/editor/EditorStudio";

export default function App() {
  const [view, setView] = useState("clip"); // 'clip' | 'editor' | 'export'
  const [clip, setClip] = useState(null);

  return (
    <Layout active={view} onNavigate={setView}>
      {view === "clip" && (
        <ClipForm
          onStartEditing={(clipData) => {
            setClip(clipData);
            setView("editor");
          }}
        />
      )}
      {view === "editor" && (
        <EditorStudio
          initialClip={clip}
          onBack={() => setView("clip")}
        />
      )}
      {view === "export" && <ExportPanel clip={clip} />}
    </Layout>
  );
}
