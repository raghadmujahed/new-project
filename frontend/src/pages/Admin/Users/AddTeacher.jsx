// src/pages/Admin/Users/AddTeacher.jsx
import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { getUser, createUser, updateUser, getTrainingSites } from "../../../services/api";
import * as XLSX from "xlsx";

export default function AddTeacher() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState("single");
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [statusMessage, setStatusMessage] = useState({ type: "", text: "" });
  const [trainingSites, setTrainingSites] = useState([]);
  const [form, setForm] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    major: "",           // تم تغيير subject إلى major
    phone: "",           // إضافة الهاتف (اختياري)
    training_site_id: "",
    role_id: 3,          // تأكد من أن 3 هو id دور المعلم في جدول roles (عدّل حسب الحاجة)
    status: "active",
  });

  const [file, setFile] = useState(null);
  const [bulkLoading, setBulkLoading] = useState(false);
  const [bulkResults, setBulkResults] = useState({ success: [], errors: [] });
  const isEditMode = !!id;

  // جلب أماكن التدريب
  useEffect(() => {
    const fetchSites = async () => {
      try {
        const res = await getTrainingSites();
        const sitesData = res?.data || res;
        setTrainingSites(Array.isArray(sitesData) ? sitesData : []);
      } catch (err) {
        console.error("فشل جلب أماكن التدريب", err);
      }
    };
    fetchSites();
  }, []);

  // جلب بيانات المعلم للتعديل
  useEffect(() => {
    if (id) {
      const fetchUser = async () => {
        try {
          const userData = await getUser(id);
          setForm({
            name: userData.name || "",
            email: userData.email || "",
            password: "",
            password_confirmation: "",
            major: userData.major || "",      // استخدم major بدلاً من subject
            phone: userData.phone || "",
            training_site_id: userData.training_site_id || "",
            role_id: userData.role_id || 3,
            status: userData.status || "active",
          });
        } catch (err) {
          console.error(err);
        }
      };
      fetchUser();
    }
  }, [id]);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
    if (errors[e.target.name]) setErrors({ ...errors, [e.target.name]: null });
    if (statusMessage.text) setStatusMessage({ type: "", text: "" });
  };

  // إضافة أو تحديث فردي
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    setStatusMessage({ type: "", text: "" });

    // التأكد من تحويل training_site_id إلى رقم (إذا كان نصاً)
    const formToSend = {
      ...form,
      training_site_id: form.training_site_id ? Number(form.training_site_id) : null,
    };

    try {
      if (id) {
        await updateUser(id, formToSend);
        setStatusMessage({ type: "success", text: "تم تحديث المعلم بنجاح" });
        setTimeout(() => navigate("/admin/users"), 1500);
      } else {
        await createUser(formToSend);
        setStatusMessage({ type: "success", text: "تمت إضافة المعلم بنجاح" });
        setForm({
          name: "", email: "", password: "", password_confirmation: "",
          major: "", phone: "", training_site_id: "", role_id: 3, status: "active",
        });
        setTimeout(() => navigate("/admin/users"), 1500);
      }
    } catch (err) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
        const errorMessages = Object.values(err.response.data.errors).flat().join(", ");
        setStatusMessage({ type: "error", text: `فشل الحفظ: ${errorMessages}` });
      } else {
        setStatusMessage({ type: "error", text: "حدث خطأ غير متوقع" });
      }
    } finally {
      setLoading(false);
    }
  };

  // الرفع الجماعي
  const handleFileChange = (e) => {
    setFile(e.target.files[0]);
    setBulkResults({ success: [], errors: [] });
    setStatusMessage({ type: "", text: "" });
  };

  const processExcel = async () => {
    if (!file) {
      alert("الرجاء اختيار ملف Excel أولاً");
      return;
    }
    setBulkLoading(true);
    setBulkResults({ success: [], errors: [] });
    setStatusMessage({ type: "", text: "" });

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
          setBulkLoading(false);
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

        // بناء خريطة اسم موقع التدريب -> ID
        const siteMap = {};
        trainingSites.forEach(site => {
          const normalized = site.name.trim();
          siteMap[normalized] = site.id;
          siteMap[normalized.toLowerCase()] = site.id;
        });

        const teachers = cleanRows.map(row => {
          let siteName = (row["مكان العمل"] || row["اسم المدرسة"] || row["training_site"] || "").trim();
          let trainingSiteId = siteMap[siteName];
          if (!trainingSiteId) trainingSiteId = siteMap[siteName.toLowerCase()];

          return {
            name: row["الاسم الكامل"] || row["name"] || "",
            email: row["البريد الإلكتروني"] || row["email"] || "",
            password: row["كلمة المرور"] || row["password"] || "12345678",
            password_confirmation: row["كلمة المرور"] || row["password"] || "12345678",
            major: row["المادة"] || row["major"] || row["subject"] || "",   // دعم subject القديم أيضاً
            phone: row["رقم الهاتف"] || row["phone"] || "",
            training_site_id: trainingSiteId,
            role_id: 3,
            status: "active",
          };
        });

        // التحقق من صحة البيانات
        const validTeachers = [];
        const invalidTeachers = [];

        teachers.forEach((teacher, idx) => {
          const missing = [];
          if (!teacher.name) missing.push("الاسم الكامل");
          if (!teacher.email) missing.push("البريد الإلكتروني");
          if (!teacher.training_site_id) missing.push("مكان العمل (غير موجود أو غير مطابق)");

          if (missing.length === 0) {
            validTeachers.push(teacher);
          } else {
            invalidTeachers.push({
              row: idx + 2,
              email: teacher.email || "غير معروف",
              missing,
            });
          }
        });

        if (invalidTeachers.length > 0) {
          const errorDetails = invalidTeachers.map(t =>
            `الصف ${t.row}: ${t.email} - ناقص: ${t.missing.join(", ")}`
          ).join("\n");
          alert(`البيانات غير كاملة في بعض الصفوف:\n${errorDetails}`);
        }

        if (validTeachers.length === 0) {
          setBulkLoading(false);
          return;
        }

        const successList = [];
        const errorList = [];

        for (const teacher of validTeachers) {
          try {
            const response = await createUser(teacher);
            successList.push({ email: teacher.email, id: response.data?.id });
          } catch (err) {
            const msg = err.response?.data?.message || err.message;
            errorList.push({ email: teacher.email, error: msg });
          }
        }

        setBulkResults({ success: successList, errors: errorList });
        if (successList.length) setFile(null);
      } catch (err) {
        console.error(err);
        alert("حدث خطأ أثناء معالجة الملف: " + err.message);
      } finally {
        setBulkLoading(false);
      }
    };
    reader.readAsArrayBuffer(file);
  };

  // وضع التعديل
  if (isEditMode) {
    return (
      <div className="user-form">
        <div className="page-header">
          <h1>تعديل معلم</h1>
          <button onClick={() => navigate("/admin/users")} className="btn-secondary">رجوع</button>
        </div>
        {statusMessage.text && (
          <div className={`status-message ${statusMessage.type}`}>{statusMessage.text}</div>
        )}
        <form onSubmit={handleSubmit} className="form">
          <div className="form-group">
            <label>الاسم الكامل *</label>
            <input type="text" name="name" value={form.name} onChange={handleChange} required />
            {errors.name && <span className="error">{errors.name[0]}</span>}
          </div>
          <div className="form-group">
            <label>البريد الإلكتروني *</label>
            <input type="email" name="email" value={form.email} onChange={handleChange} required />
            {errors.email && <span className="error">{errors.email[0]}</span>}
          </div>
          <div className="form-group">
            <label>المادة *</label>
            <input type="text" name="major" value={form.major} onChange={handleChange} required />
            {errors.major && <span className="error">{errors.major[0]}</span>}
          </div>
          <div className="form-group">
            <label>مكان العمل *</label>
            <select name="training_site_id" value={form.training_site_id} onChange={handleChange} required>
              <option value="">اختر مكان العمل</option>
              {trainingSites.map(site => (
                <option key={site.id} value={site.id}>{site.name}</option>
              ))}
            </select>
            {errors.training_site_id && <span className="error">{errors.training_site_id[0]}</span>}
          </div>
          <div className="form-group">
            <label>رقم الهاتف (اختياري)</label>
            <input type="tel" name="phone" value={form.phone} onChange={handleChange} />
            {errors.phone && <span className="error">{errors.phone[0]}</span>}
          </div>
          <div className="form-group">
            <label>كلمة المرور (اتركها فارغة إذا لم ترد التغيير)</label>
            <input type="password" name="password" value={form.password} onChange={handleChange} />
            {errors.password && <span className="error">{errors.password[0]}</span>}
          </div>
          <div className="form-group">
            <label>تأكيد كلمة المرور</label>
            <input type="password" name="password_confirmation" value={form.password_confirmation} onChange={handleChange} />
          </div>
          <div className="form-actions">
            <button type="submit" disabled={loading}>{loading ? "جاري الحفظ..." : "تحديث"}</button>
            <button type="button" onClick={() => navigate("/admin/users")}>إلغاء</button>
          </div>
        </form>
      </div>
    );
  }

  // وضع الإضافة (فردي / جماعي)
  return (
    <div className="user-form">
      <div className="page-header">
        <h1>إضافة معلم جديد</h1>
        <button onClick={() => navigate("/admin/users")} className="btn-secondary">رجوع</button>
      </div>

      {statusMessage.text && (
        <div className={`status-message ${statusMessage.type}`}>{statusMessage.text}</div>
      )}

      <div className="tabs">
        <button
          className={activeTab === "single" ? "tab-active" : "tab"}
          onClick={() => setActiveTab("single")}
        >
          إضافة معلم واحد
        </button>
        <button
          className={activeTab === "bulk" ? "tab-active" : "tab"}
          onClick={() => setActiveTab("bulk")}
        >
          رفع مجموعة من ملف Excel
        </button>
      </div>

      {activeTab === "single" && (
        <form onSubmit={handleSubmit} className="form">
          <div className="form-group">
            <label>الاسم الكامل *</label>
            <input type="text" name="name" value={form.name} onChange={handleChange} required />
            {errors.name && <span className="error">{errors.name[0]}</span>}
          </div>
          <div className="form-group">
            <label>البريد الإلكتروني *</label>
            <input type="email" name="email" value={form.email} onChange={handleChange} required />
            {errors.email && <span className="error">{errors.email[0]}</span>}
          </div>
          <div className="form-group">
            <label>المادة *</label>
            <input type="text" name="major" value={form.major} onChange={handleChange} required />
            {errors.major && <span className="error">{errors.major[0]}</span>}
          </div>
          <div className="form-group">
            <label>مكان العمل *</label>
            <select name="training_site_id" value={form.training_site_id} onChange={handleChange} required>
              <option value="">اختر مكان العمل</option>
              {trainingSites.map(site => (
                <option key={site.id} value={site.id}>{site.name}</option>
              ))}
            </select>
            {errors.training_site_id && <span className="error">{errors.training_site_id[0]}</span>}
          </div>
          <div className="form-group">
            <label>رقم الهاتف (اختياري)</label>
            <input type="tel" name="phone" value={form.phone} onChange={handleChange} />
            {errors.phone && <span className="error">{errors.phone[0]}</span>}
          </div>
          <div className="form-group">
            <label>كلمة المرور *</label>
            <input type="password" name="password" value={form.password} onChange={handleChange} required />
            {errors.password && <span className="error">{errors.password[0]}</span>}
          </div>
          <div className="form-group">
            <label>تأكيد كلمة المرور *</label>
            <input type="password" name="password_confirmation" value={form.password_confirmation} onChange={handleChange} required />
          </div>
          <div className="form-actions">
            <button type="submit" disabled={loading}>{loading ? "جاري الحفظ..." : "إضافة"}</button>
            <button type="button" onClick={() => navigate("/admin/users")}>إلغاء</button>
          </div>
        </form>
      )}

      {activeTab === "bulk" && (
        <div className="bulk-section">
          <p>قم بتحميل ملف Excel يحتوي على الأعمدة التالية:</p>
          <ul>
            <li><strong>الاسم الكامل</strong> (مطلوب)</li>
            <li><strong>البريد الإلكتروني</strong> (مطلوب)</li>
            <li><strong>المادة</strong> (مطلوب)</li>
            <li><strong>مكان العمل</strong> (مطلوب، يجب أن يطابق اسم موقع تدريب مسجل)</li>
            <li><strong>رقم الهاتف</strong> (اختياري)</li>
            <li><strong>كلمة المرور</strong> (اختياري، افتراضي 12345678)</li>
          </ul>
          <input type="file" accept=".xlsx, .xls" onChange={handleFileChange} />
          <button onClick={processExcel} disabled={bulkLoading} className="btn-primary">
            {bulkLoading ? "جاري الرفع..." : "رفع والإضافة"}
          </button>

          {bulkResults.success.length > 0 && (
            <div className="success-box">✅ تمت إضافة {bulkResults.success.length} معلم بنجاح</div>
          )}
          {bulkResults.errors.length > 0 && (
            <div className="error-box">
              ❌ فشلت إضافة {bulkResults.errors.length} معلم
              <ul>
                {bulkResults.errors.map((e, idx) => (
                  <li key={idx}><strong>{e.email}</strong> : {e.error}</li>
                ))}
              </ul>
            </div>
          )}
        </div>
      )}
    </div>
  );
}