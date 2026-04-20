// src/components/admin/BulkUploadModal.jsx
import { useState } from "react";
import * as XLSX from "xlsx";
import { createUser } from "../../services/api";

export default function BulkUploadModal({ isOpen, onClose, onSuccess, roleId = 5 }) {
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

        // تنظيف أسماء الأعمدة من المسافات الزائدة
        const cleanRows = rows.map(row => {
          const clean = {};
          Object.keys(row).forEach(key => {
            const cleanKey = key.trim();
            clean[cleanKey] = row[key];
          });
          return clean;
        });

        // تحويل الصفوف إلى كائنات المستخدم
        const users = cleanRows.map((row) => ({
          name: row["الاسم الكامل"] || row["name"] || "",
          email: row["البريد الإلكتروني"] || row["email"] || "",
          phone: row["رقم الهاتف"] || row["phone"] || "",              // إضافة الهاتف
          password: row["كلمة المرور"] || row["password"] || "12345678",
          password_confirmation: row["كلمة المرور"] || row["password"] || "12345678",
          university_id: String(row["الرقم الجامعي"] || row["university_id"] || ""),  // تحويل إلى نص
          major: row["التخصص"] || row["major"] || "",
          role_id: roleId,
          status: "active",
        }));

        // التحقق من البيانات الأساسية حسب الدور
        const isStudent = (roleId == 2); // افتراض أن role_id=2 للطالب
        const validUsers = [];
        const invalidUsers = [];

        users.forEach((user, idx) => {
          const missing = [];
          if (!user.name) missing.push("الاسم الكامل");
          if (!user.email) missing.push("البريد الإلكتروني");
          if (!user.university_id) missing.push("الرقم الجامعي");
          if (isStudent && !user.major) missing.push("التخصص");

          if (missing.length === 0) {
            validUsers.push(user);
          } else {
            invalidUsers.push({
              row: idx + 2,
              email: user.email || "غير معروف",
              missing,
            });
          }
        });

        if (invalidUsers.length > 0) {
          const errorDetails = invalidUsers.map(u =>
            `الصف ${u.row}: ${u.email} - ناقص: ${u.missing.join(", ")}`
          ).join("\n");
          alert(`البيانات غير كاملة في بعض الصفوف:\n${errorDetails}`);
        }

        if (validUsers.length === 0) {
          setLoading(false);
          return;
        }

        const successList = [];
        const errorList = [];

        // إرسال كل مستخدم على حدة
        for (const user of validUsers) {
          try {
            const response = await createUser(user);
            successList.push({ email: user.email, id: response.data?.id });
          } catch (err) {
            const msg = err.response?.data?.message || err.message;
            errorList.push({ email: user.email, error: msg });
          }
        }

        setResults({ success: successList, errors: errorList });
        if (successList.length) {
          onSuccess?.(); // تحديث القائمة الرئيسية
          setFile(null);
          // إذا لم يغلق النافذة تلقائياً، يبقى المستخدم يرى النتائج
        }
      } catch (err) {
        console.error(err);
        alert("حدث خطأ أثناء معالجة الملف: " + err.message);
      } finally {
        setLoading(false);
      }
    };

    reader.readAsArrayBuffer(file);  // استخدام readAsArrayBuffer بدلاً من readAsBinaryString
  };

  if (!isOpen) return null;

  return (
    <div className="modal-overlay">
      <div className="modal-content">
        <div className="modal-header">
          <h3>استيراد مستخدمين من ملف Excel</h3>
          <button className="close-btn" onClick={onClose}>×</button>
        </div>
        <div className="modal-body">
          <p>قم بتحميل ملف Excel يحتوي على الأعمدة التالية:</p>
          <ul>
            <li><strong>الاسم الكامل</strong> (مطلوب)</li>
            <li><strong>البريد الإلكتروني</strong> (مطلوب)</li>
            <li><strong>الرقم الجامعي</strong> (مطلوب)</li>
            <li><strong>رقم الهاتف</strong> (اختياري)</li>
            <li><strong>التخصص</strong> (مطلوب للطلاب، اختياري لبقية الأدوار)</li>
            <li><strong>كلمة المرور</strong> (اختياري، افتراضي 12345678)</li>
          </ul>
          <input type="file" accept=".xlsx, .xls" onChange={handleFileChange} />
          <button onClick={processExcel} disabled={loading} className="btn-primary">
            {loading ? "جاري الرفع..." : "رفع والإضافة"}
          </button>

          {results.success.length > 0 && (
            <div className="success-box">
              <h4>✅ تمت إضافة {results.success.length} مستخدم بنجاح</h4>
            </div>
          )}
          {results.errors.length > 0 && (
            <div className="error-box">
              <h4>❌ فشلت إضافة {results.errors.length} مستخدم</h4>
              <ul>
                {results.errors.map((e, idx) => (
                  <li key={idx}><strong>{e.email}</strong> : {e.error}</li>
                ))}
              </ul>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}