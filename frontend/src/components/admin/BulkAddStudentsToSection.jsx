// src/pages/Admin/Sections/AddStudentsToSection.jsx
import { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { getSection, createUser, enrollStudentInSection, getUsers } from "../../../services/api";
import * as XLSX from "xlsx";

export default function AddStudentsToSection() {
  const { id: sectionId } = useParams();
  const navigate = useNavigate();
  const [section, setSection] = useState(null);
  const [loading, setLoading] = useState(false);
  const [manualStudent, setManualStudent] = useState({ name: "", email: "", university_id: "" });
  const [studentsList, setStudentsList] = useState([]);
  const [bulkFile, setBulkFile] = useState(null);
  const [bulkLoading, setBulkLoading] = useState(false);
  const [results, setResults] = useState(null);

  useEffect(() => {
    const fetchSection = async () => {
      try {
        const data = await getSection(sectionId);
        setSection(data);
      } catch (err) {
        console.error(err);
        alert("فشل تحميل بيانات الشعبة");
      }
    };
    fetchSection();
  }, [sectionId]);

  // إضافة طالب يدوي (واحد)
  const handleManualAdd = async () => {
    if (!manualStudent.name || !manualStudent.email || !manualStudent.university_id) {
      alert("املأ جميع الحقول");
      return;
    }
    setLoading(true);
    try {
      let userId = null;
      const existing = await getUsers({ email: manualStudent.email });
      if (existing.data && existing.data.length > 0) {
        userId = existing.data[0].id;
      } else {
        const newUser = await createUser({
          name: manualStudent.name,
          email: manualStudent.email,
          university_id: manualStudent.university_id,
          password: "12345678",
          password_confirmation: "12345678",
          role_id: 2,
          status: "active",
        });
        userId = newUser.data.id;
      }
      await enrollStudentInSection(sectionId, userId);
      alert("تمت إضافة الطالب بنجاح");
      setManualStudent({ name: "", email: "", university_id: "" });
      // يمكن إعادة توجيه أو البقاء
    } catch (err) {
      alert("فشل إضافة الطالب: " + err.message);
    } finally {
      setLoading(false);
    }
  };

  // معالجة ملف Excel (إضافة عدة طلاب)
  const handleBulkFileChange = (e) => {
    setBulkFile(e.target.files[0]);
    setResults(null);
  };

  const processBulkUpload = async () => {
    if (!bulkFile) return alert("اختر ملف Excel أولاً");
    setBulkLoading(true);
    const reader = new FileReader();
    reader.onload = async (evt) => {
      try {
        const data = new Uint8Array(evt.target.result);
        const workbook = XLSX.read(data, { type: "array" });
        const rows = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]]);
        const students = rows.map(row => ({
          name: row["الاسم الكامل"] || row["name"] || "",
          email: row["البريد الإلكتروني"] || row["email"] || "",
          university_id: String(row["الرقم الجامعي"] || row["university_id"] || ""),
        })).filter(s => s.name && s.email && s.university_id);

        if (students.length === 0) throw new Error("لا توجد بيانات صالحة");

        const successList = [], errorList = [];
        for (const s of students) {
          try {
            let userId = null;
            const existing = await getUsers({ email: s.email });
            if (existing.data && existing.data.length > 0) {
              userId = existing.data[0].id;
            } else {
              const newUser = await createUser({
                name: s.name, email: s.email, university_id: s.university_id,
                password: "12345678", password_confirmation: "12345678",
                role_id: 2, status: "active"
              });
              userId = newUser.data.id;
            }
            await enrollStudentInSection(sectionId, userId);
            successList.push(s.email);
          } catch (err) {
            errorList.push({ email: s.email, error: err.message });
          }
        }
        setResults({ success: successList, errors: errorList });
        if (successList.length) {
          alert(`تمت إضافة ${successList.length} طالب بنجاح`);
        }
      } catch (err) {
        alert(err.message);
      } finally {
        setBulkLoading(false);
      }
    };
    reader.readAsArrayBuffer(bulkFile);
  };

  if (!section) return <div>جاري تحميل بيانات الشعبة...</div>;

  return (
    <div className="add-students-to-section">
      <div className="page-header">
        <h1>إضافة طلاب إلى الشعبة: {section.name}</h1>
        <button onClick={() => navigate("/admin/sections")} className="btn-secondary">رجوع</button>
      </div>

      {/* إضافة طالب يدوي */}
      <fieldset style={{ border: "1px solid #ccc", padding: "1rem", marginBottom: "1.5rem" }}>
        <legend>إضافة طالب واحد</legend>
        <div className="form-row">
          <input type="text" placeholder="الاسم الكامل" value={manualStudent.name} onChange={(e) => setManualStudent({ ...manualStudent, name: e.target.value })} />
          <input type="email" placeholder="البريد الإلكتروني" value={manualStudent.email} onChange={(e) => setManualStudent({ ...manualStudent, email: e.target.value })} />
          <input type="text" placeholder="الرقم الجامعي" value={manualStudent.university_id} onChange={(e) => setManualStudent({ ...manualStudent, university_id: e.target.value })} />
          <button onClick={handleManualAdd} disabled={loading}>{loading ? "جاري..." : "إضافة طالب"}</button>
        </div>
      </fieldset>

      {/* رفع ملف Excel */}
      <fieldset style={{ border: "1px solid #ccc", padding: "1rem" }}>
        <legend>رفع ملف Excel (إضافة عدة طلاب)</legend>
        <p>الأعمدة المطلوبة: <strong>الاسم الكامل, البريد الإلكتروني, الرقم الجامعي</strong></p>
        <input type="file" accept=".xlsx, .xls" onChange={handleBulkFileChange} />
        <button onClick={processBulkUpload} disabled={bulkLoading} className="btn-primary" style={{ marginTop: "0.5rem" }}>
          {bulkLoading ? "جاري الرفع..." : "رفع وإضافة الطلاب"}
        </button>
        {results && (
          <div>
            <div className="success-box">✅ تمت إضافة {results.success.length} طالب</div>
            {results.errors.length > 0 && (
              <div className="error-box">
                ❌ فشل إضافة {results.errors.length} طالب
                <ul>{results.errors.map((e, i) => <li key={i}>{e.email}: {e.error}</li>)}</ul>
              </div>
            )}
          </div>
        )}
      </fieldset>
    </div>
  );
}