// src/pages/Admin/TrainingSites/TrainingSiteForm.jsx
import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { getTrainingSite, createTrainingSite, updateTrainingSite } from "../../../services/api";
import * as XLSX from "xlsx";

export default function TrainingSiteForm() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [form, setForm] = useState({
    name: "",
    location: "",
    phone: "",
    description: "",
    directorate: "وسط",
    capacity: 10,
    site_type: "school",
    governing_body: "directorate_of_education",
    is_active: true,
  });
  const [loading, setLoading] = useState(false);
  const [bulkLoading, setBulkLoading] = useState(false);
  const [bulkFile, setBulkFile] = useState(null);
  const [bulkResults, setBulkResults] = useState(null);

  // تحميل البيانات إذا كان تعديل
  useEffect(() => {
    if (id) {
      getTrainingSite(id).then((data) => setForm(data));
    }
  }, [id]);

  // الإضافة / التعديل الفردي
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    try {
      if (id) await updateTrainingSite(id, form);
      else await createTrainingSite(form);
      navigate("/admin/training-sites");
    } catch (err) {
      console.error(err);
      alert("حدث خطأ أثناء الحفظ");
    } finally {
      setLoading(false);
    }
  };

  // معالجة رفع ملف Excel
  const handleFileChange = (e) => setBulkFile(e.target.files[0]);

  const processBulkUpload = async () => {
    if (!bulkFile) return alert("اختر ملف Excel أولاً");
    setBulkLoading(true);
    setBulkResults(null);
    const reader = new FileReader();
    reader.onload = async (evt) => {
      try {
        const data = new Uint8Array(evt.target.result);
        const workbook = XLSX.read(data, { type: "array" });
        const rows = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
        const sites = rows.map((row) => ({
          name: row["الاسم"] || row["name"] || "",
          location: row["الموقع"] || row["location"] || "",
          phone: row["الهاتف"] || row["phone"] || "",
          description: row["الوصف"] || row["description"] || "",
          directorate: row["المديرية"] || row["directorate"] || "وسط",
          capacity: parseInt(row["السعة"] || row["capacity"] || 10),
          site_type: row["نوع الموقع"] === "مركز صحي" ? "health_center" : "school",
          governing_body: row["الجهة المسؤولة"] === "وزارة الصحة" ? "ministry_of_health" : "directorate_of_education",
          is_active: (row["نشط"] === "نعم" || row["is_active"] === true || row["is_active"] === "true") ? true : false,
        })).filter(s => s.name); // الاسم إجباري

        if (sites.length === 0) throw new Error("لا توجد بيانات صالحة (الاسم مطلوب)");

        const successList = [];
        const errorList = [];
        for (const site of sites) {
          try {
            await createTrainingSite(site);
            successList.push(site.name);
          } catch (err) {
            errorList.push({ name: site.name, error: err.response?.data?.message || err.message });
          }
        }
        setBulkResults({ success: successList, errors: errorList });
        if (successList.length) alert(`تمت إضافة ${successList.length} موقع بنجاح`);
      } catch (err) {
        alert(err.message);
      } finally {
        setBulkLoading(false);
        setBulkFile(null);
        // إعادة تعيين حقل الملف
        document.getElementById("bulk-file-input").value = "";
      }
    };
    reader.readAsArrayBuffer(bulkFile);
  };

  return (
    <div>
      <div className="page-header">
        <h1>{id ? "تعديل موقع تدريب" : "إضافة مواقع تدريب (فردي / جماعي)"}</h1>
        <button onClick={() => navigate("/admin/training-sites")} className="btn-secondary">
          رجوع
        </button>
      </div>

      {/* نموذج الإضافة الفردية */}
      <form onSubmit={handleSubmit} className="form" style={{ marginBottom: "2rem", border: "1px solid #ccc", padding: "1rem", borderRadius: "8px" }}>
        <h3>إضافة فردية</h3>
        <div className="form-group">
          <label>الاسم *</label>
          <input type="text" value={form.name} onChange={(e) => setForm({ ...form, name: e.target.value })} required />
        </div>
        <div className="form-group">
          <label>الموقع</label>
          <input type="text" value={form.location} onChange={(e) => setForm({ ...form, location: e.target.value })} />
        </div>
        <div className="form-group">
          <label>الهاتف</label>
          <input type="text" value={form.phone} onChange={(e) => setForm({ ...form, phone: e.target.value })} />
        </div>
        <div className="form-group">
          <label>الوصف</label>
          <textarea value={form.description} onChange={(e) => setForm({ ...form, description: e.target.value })} />
        </div>
        <div className="form-group">
          <label>المديرية</label>
          <select value={form.directorate} onChange={(e) => setForm({ ...form, directorate: e.target.value })}>
            <option value="وسط">وسط</option>
            <option value="شمال">شمال</option>
            <option value="جنوب">جنوب</option>
            <option value="يطا">يطا</option>
          </select>
        </div>
        <div className="form-group">
          <label>السعة</label>
          <input type="number" value={form.capacity} onChange={(e) => setForm({ ...form, capacity: parseInt(e.target.value) })} />
        </div>
        <div className="form-group">
          <label>نوع الموقع</label>
          <select value={form.site_type} onChange={(e) => setForm({ ...form, site_type: e.target.value })}>
            <option value="school">مدرسة</option>
            <option value="health_center">مركز صحي</option>
          </select>
        </div>
        <div className="form-group">
          <label>الجهة المسؤولة</label>
          <select value={form.governing_body} onChange={(e) => setForm({ ...form, governing_body: e.target.value })}>
            <option value="directorate_of_education">مديرية التربية</option>
            <option value="ministry_of_health">وزارة الصحة</option>
          </select>
        </div>
        <div className="form-group">
          <label>
            <input type="checkbox" checked={form.is_active} onChange={(e) => setForm({ ...form, is_active: e.target.checked })} />
            نشط
          </label>
        </div>
        <button type="submit" disabled={loading}>
          {loading ? "جاري الحفظ..." : id ? "تحديث" : "إضافة موقع"}
        </button>
      </form>

      {/* قسم الإضافة الجماعية */}
      <fieldset style={{ border: "1px solid #ccc", padding: "1rem", borderRadius: "8px" }}>
        <legend style={{ fontWeight: "bold" }}>إضافة جماعية عبر ملف Excel</legend>
        <p>
          الأعمدة المطلوبة: <strong>الاسم</strong> (إجباري)، الموقع، الهاتف، الوصف، المديرية، السعة، نوع الموقع (مدرسة/مركز صحي)، الجهة المسؤولة (مديرية التربية/وزارة الصحة)، نشط (نعم/لا).
        </p>
        <input type="file" id="bulk-file-input" accept=".xlsx, .xls" onChange={handleFileChange} />
        <button onClick={processBulkUpload} disabled={bulkLoading} style={{ marginTop: "0.5rem" }} className="btn-secondary">
          {bulkLoading ? "جاري الرفع..." : "رفع وإضافة"}
        </button>
        {bulkResults && (
          <div style={{ marginTop: "1rem" }}>
            <div style={{ color: "green" }}>✅ نجح: {bulkResults.success.length} موقع</div>
            {bulkResults.errors.length > 0 && (
              <div style={{ color: "red" }}>
                ❌ فشل: {bulkResults.errors.map((e) => `${e.name} (${e.error})`).join("; ")}
              </div>
            )}
          </div>
        )}
      </fieldset>
    </div>
  );
}