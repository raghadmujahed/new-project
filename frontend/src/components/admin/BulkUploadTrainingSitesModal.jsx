import { useState } from "react";
import * as XLSX from "xlsx";
import { createTrainingSite } from "../../services/api";

export default function BulkUploadTrainingSitesModal({ isOpen, onClose, onSuccess }) {
  const [file, setFile] = useState(null);
  const [loading, setLoading] = useState(false);
  const [results, setResults] = useState({ success: [], errors: [] });

  const handleFileChange = (e) => {
    setFile(e.target.files[0]);
    setResults({ success: [], errors: [] });
  };

  const processExcel = async () => {
    if (!file) {
      alert("الرجاء اختيار ملف Excel أولاً");
      return;
    }
    setLoading(true);
    setResults({ success: [], errors: [] });

    const reader = new FileReader();
    reader.onload = async (evt) => {
      try {
        const arrayBuffer = evt.target.result;
        const workbook = XLSX.read(arrayBuffer, { type: "array" });
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];
        let rows = XLSX.utils.sheet_to_json(worksheet);

        if (rows.length === 0) {
          alert("الملف فارغ أو لا يحتوي على بيانات");
          setLoading(false);
          return;
        }

        // تنظيف أسماء الأعمدة
        const cleanRows = rows.map(row => {
          const clean = {};
          Object.keys(row).forEach(key => {
            const cleanKey = key.trim();
            clean[cleanKey] = row[key];
          });
          return clean;
        });

        const sites = cleanRows.map(row => ({
          name: row["الاسم"] || row["name"] || "",
          location: row["الموقع"] || row["location"] || "",
          phone: row["الهاتف"] || row["phone"] || "",
          directorate: row["المديرية"] || row["directorate"] || "وسط",
          capacity: parseInt(row["السعة"] || row["capacity"] || 10),
          site_type: row["النوع"] || row["site_type"] || "school",
          governing_body: row["الجهة"] || row["governing_body"] || "directorate_of_education",
          is_active: row["نشط"] !== undefined ? (row["نشط"] === "نعم" || row["نشط"] === true) : true,
        })).filter(s => s.name); // الاسم مطلوب

        if (sites.length === 0) {
          alert("لا توجد بيانات صالحة (يجب أن يحتوي كل صف على اسم الموقع)");
          setLoading(false);
          return;
        }

        const successList = [];
        const errorList = [];

        for (const site of sites) {
          try {
            const response = await createTrainingSite(site);
            successList.push({ name: site.name, id: response.data?.id });
          } catch (err) {
            const msg = err.response?.data?.message || err.message;
            errorList.push({ name: site.name, error: msg });
          }
        }

        setResults({ success: successList, errors: errorList });
        if (successList.length) {
          onSuccess?.(); // تحديث القائمة
          setFile(null);
        }
      } catch (err) {
        console.error(err);
        alert("حدث خطأ أثناء معالجة الملف: " + err.message);
      } finally {
        setLoading(false);
      }
    };
    reader.readAsArrayBuffer(file);
  };

  if (!isOpen) return null;

  return (
    <div className="modal-overlay">
      <div className="modal-content">
        <div className="modal-header">
          <h3>استيراد مواقع تدريب من ملف Excel</h3>
          <button className="close-btn" onClick={onClose}>×</button>
        </div>
        <div className="modal-body">
          <p>قم بتحميل ملف Excel يحتوي على الأعمدة التالية:</p>
          <ul>
            <li><strong>الاسم</strong> (مطلوب)</li>
            <li><strong>الموقع</strong> (اختياري)</li>
            <li><strong>الهاتف</strong> (اختياري)</li>
            <li><strong>المديرية</strong> (وسط/شمال/جنوب/يطا، افتراضي وسط)</li>
            <li><strong>السعة</strong> (عدد، افتراضي 10)</li>
            <li><strong>النوع</strong> (school / health_center، افتراضي school)</li>
            <li><strong>الجهة</strong> (directorate_of_education / ministry_of_health، افتراضي directorate_of_education)</li>
            <li><strong>نشط</strong> (نعم/لا أو true/false، افتراضي نعم)</li>
          </ul>
          <input type="file" accept=".xlsx, .xls" onChange={handleFileChange} />
          <button onClick={processExcel} disabled={loading} className="btn-primary">
            {loading ? "جاري الرفع..." : "رفع والإضافة"}
          </button>

          {results.success.length > 0 && (
            <div className="success-box">
              <h4>✅ تمت إضافة {results.success.length} موقع تدريب بنجاح</h4>
            </div>
          )}
          {results.errors.length > 0 && (
            <div className="error-box">
              <h4>❌ فشلت إضافة {results.errors.length} موقع</h4>
              <ul>
                {results.errors.map((e, idx) => (
                  <li key={idx}><strong>{e.name}</strong> : {e.error}</li>
                ))}
              </ul>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}

