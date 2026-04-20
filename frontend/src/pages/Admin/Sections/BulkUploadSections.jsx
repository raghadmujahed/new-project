// src/pages/Admin/Sections/BulkUploadSections.jsx
import { useState } from "react";
import { useNavigate } from "react-router-dom";
import { bulkUploadSections } from "../../../services/api";
import * as XLSX from "xlsx";

export default function BulkUploadSections() {
  const navigate = useNavigate();
  const [file, setFile] = useState(null);
  const [loading, setLoading] = useState(false);
  const [results, setResults] = useState(null);

  const handleFileChange = (e) => setFile(e.target.files[0]);

  const processFile = async () => {
    if (!file) return alert("اختر ملف أولاً");
    setLoading(true);
    const reader = new FileReader();
    reader.onload = async (evt) => {
      try {
        const data = new Uint8Array(evt.target.result);
        const workbook = XLSX.read(data, { type: "array" });
        const rows = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
        if (rows.length === 0) throw new Error("الملف فارغ");
        const response = await bulkUploadSections({ sections: rows });
        setResults(response.data);
        if (response.data.success.length) {
          setTimeout(() => {
            navigate("/admin/sections");
          }, 2000);
        }
      } catch (err) {
        alert(err.message);
      } finally {
        setLoading(false);
      }
    };
    reader.readAsArrayBuffer(file);
  };

  return (
    <div>
      <div className="page-header">
        <h1>رفع شعب من Excel</h1>
        <button onClick={() => navigate("/admin/sections")}>رجوع</button>
      </div>
      <div>
        <p>
          الأعمدة المطلوبة: <strong>section_name, academic_year, semester, course_id, academic_supervisor_id (اختياري)</strong>
        </p>
        <ul>
          <li>semester: <code>first</code> أو <code>second</code> فقط</li>
        </ul>
        <input type="file" accept=".xlsx, .xls" onChange={handleFileChange} />
        <button onClick={processFile} disabled={loading}>
          {loading ? "جاري..." : "رفع"}
        </button>
        {results && (
          <div>
            {results.success.length > 0 && (
              <div className="success">✅ تمت إضافة {results.success.length} شعبة</div>
            )}
            {results.failed.length > 0 && (
              <div className="error">
                ❌ فشلت {results.failed.length} شعبة:{" "}
                {results.failed.map((f) => f.section_name).join(", ")}
              </div>
            )}
          </div>
        )}
      </div>
    </div>
  );
}