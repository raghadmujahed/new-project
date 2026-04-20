// src/pages/Admin/Users/AddStudent.jsx
import { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { getUser, createUser, updateUser, getDepartments } from "../../../services/api";
import * as XLSX from "xlsx";

export default function AddStudent() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [activeTab, setActiveTab] = useState("single");
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState({});
  const [statusMessage, setStatusMessage] = useState({ type: "", text: "" });
  const [departments, setDepartments] = useState([]);
  const [form, setForm] = useState({
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    university_id: "",
    major: "",
    department_id: "",
    role_id: 2,
    status: "active",
  });
  const [file, setFile] = useState(null);
  const [bulkLoading, setBulkLoading] = useState(false);
  const [bulkResults, setBulkResults] = useState({ success: [], errors: [] });
  const isEditMode = !!id;

  // جلب الأقسام من الخادم
  useEffect(() => {
    const fetchDepartments = async () => {
      try {
        const res = await getDepartments();
        const departmentsData = res?.data || res;
        setDepartments(Array.isArray(departmentsData) ? departmentsData : []);
      } catch (err) {
        console.error("فشل جلب الأقسام", err);
      }
    };
    fetchDepartments();
  }, []);

  // جلب بيانات الطالب للتعديل
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
            university_id: userData.university_id || "",
            major: userData.major || "",
            department_id: userData.department_id || "",
            role_id: userData.role_id || 2,
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

  // إرسال النموذج الفردي
  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    setStatusMessage({ type: "", text: "" });

    // تحويل university_id إلى نص قبل الإرسال
    const formToSend = {
      ...form,
      university_id: String(form.university_id || ""),
    };

    try {
      if (id) {
        await updateUser(id, formToSend);
        setStatusMessage({ type: "success", text: "تم تحديث الطالب بنجاح" });
      } else {
        await createUser(formToSend);
        setStatusMessage({ type: "success", text: "تمت إضافة الطالب بنجاح" });
        setForm({
          name: "",
          email: "",
          password: "",
          password_confirmation: "",
          university_id: "",
          major: "",
          department_id: "",
          role_id: 2,
          status: "active",
        });
      }
      setTimeout(() => {
        navigate("/admin/users");
      }, 1500);
    } catch (err) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
        const errorMessages = Object.values(err.response.data.errors).flat().join(", ");
        setStatusMessage({ type: "error", text: `فشل الحفظ: ${errorMessages}` });
      } else {
        setStatusMessage({ type: "error", text: "حدث خطأ غير متوقع أثناء حفظ المستخدم" });
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
        
        // قراءة البيانات مع دعم وجود رأس أو بدونه
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

        // بناء خريطة اسم القسم -> ID (مع تجاهل حالة الأحرف والمسافات)
        const departmentMap = {};
        departments.forEach(dept => {
          const normalizedName = dept.name.trim();
          departmentMap[normalizedName] = dept.id;
          departmentMap[normalizedName.toLowerCase()] = dept.id;
        });

        const students = cleanRows.map((row) => {
          // محاولة قراءة الأعمدة بأسماء مختلفة
          const deptName = (row["القسم"] || row["قسم"] || row["department"] || "").trim();
          let departmentId = departmentMap[deptName];
          if (!departmentId) departmentId = departmentMap[deptName.toLowerCase()];
          
          return {
            name: row["الاسم الكامل"] || row["الاسم"] || row["name"] || "",
            email: row["البريد الإلكتروني"] || row["البريد"] || row["email"] || "",
            password: row["كلمة المرور"] || row["password"] || "12345678",
            password_confirmation: row["كلمة المرور"] || row["password"] || "12345678",
            university_id: String(row["الرقم الجامعي"] || row["university_id"] || ""),
            major: row["التخصص"] || row["major"] || "",
            department_id: departmentId,
            role_id: 2,
            status: "active",
          };
        });

        // التحقق من صحة كل طالب مع تسجيل الأخطاء التفصيلية
        const validStudents = [];
        const invalidStudents = [];

        students.forEach((student, idx) => {
          const missing = [];
          if (!student.name) missing.push("الاسم الكامل");
          if (!student.email) missing.push("البريد الإلكتروني");
          if (!student.university_id) missing.push("الرقم الجامعي");
          if (!student.department_id) missing.push("القسم (غير موجود أو غير مطابق)");
          if (!student.major) missing.push("التخصص");

          if (missing.length === 0) {
            validStudents.push(student);
          } else {
            invalidStudents.push({ 
              row: idx + 2, 
              email: student.email || "غير معروف", 
              missing 
            });
          }
        });

        if (invalidStudents.length > 0) {
          const errorDetails = invalidStudents.map(s => 
            `الصف ${s.row}: ${s.email} - ناقص: ${s.missing.join(", ")}`
          ).join("\n");
          alert(`البيانات غير كاملة في بعض الصفوف:\n${errorDetails}`);
        }

        if (validStudents.length === 0) {
          setBulkLoading(false);
          return;
        }

        const successList = [];
        const errorList = [];

        for (const student of validStudents) {
          try {
            const response = await createUser(student);
            successList.push({ email: student.email, id: response.data?.id });
          } catch (err) {
            const msg = err.response?.data?.message || err.message;
            errorList.push({ email: student.email, error: msg });
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
          <h1>تعديل طالب</h1>
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
            <label>الرقم الجامعي</label>
            <input type="text" name="university_id" value={form.university_id} onChange={handleChange} />
            {errors.university_id && <span className="error">{errors.university_id[0]}</span>}
          </div>
          <div className="form-group">
            <label>القسم</label>
            <select name="department_id" value={form.department_id} onChange={handleChange}>
              <option value="">اختر القسم</option>
              {departments.map(dept => (
                <option key={dept.id} value={dept.id}>{dept.name}</option>
              ))}
            </select>
            {errors.department_id && <span className="error">{errors.department_id[0]}</span>}
          </div>
          <div className="form-group">
            <label>التخصص</label>
            <input type="text" name="major" value={form.major} onChange={handleChange} />
            {errors.major && <span className="error">{errors.major[0]}</span>}
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
        <h1>إضافة طالب جديد</h1>
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
          إضافة طالب واحد
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
            <label>الرقم الجامعي *</label>
            <input type="text" name="university_id" value={form.university_id} onChange={handleChange} required />
            {errors.university_id && <span className="error">{errors.university_id[0]}</span>}
          </div>
          <div className="form-group">
            <label>القسم *</label>
            <select name="department_id" value={form.department_id} onChange={handleChange} required>
              <option value="">اختر القسم</option>
              {departments.map(dept => (
                <option key={dept.id} value={dept.id}>{dept.name}</option>
              ))}
            </select>
            {errors.department_id && <span className="error">{errors.department_id[0]}</span>}
          </div>
          <div className="form-group">
            <label>التخصص *</label>
            <input type="text" name="major" value={form.major} onChange={handleChange} required />
            {errors.major && <span className="error">{errors.major[0]}</span>}
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
            <li><strong>الرقم الجامعي</strong> (مطلوب)</li>
            <li><strong>القسم</strong> (مطلوب، يجب أن يكون مطابقاً لاسم القسم في قاعدة البيانات)</li>
            <li><strong>التخصص</strong> (مطلوب)</li>
            <li><strong>كلمة المرور</strong> (اختياري، افتراضي 12345678)</li>
          </ul>
          <input type="file" accept=".xlsx, .xls" onChange={handleFileChange} />
          <button onClick={processExcel} disabled={bulkLoading} className="btn-primary">
            {bulkLoading ? "جاري الرفع..." : "رفع والإضافة"}
          </button>

          {bulkResults.success.length > 0 && (
            <div className="success-box">✅ تمت إضافة {bulkResults.success.length} طالب بنجاح</div>
          )}
          {bulkResults.errors.length > 0 && (
            <div className="error-box">
              ❌ فشلت إضافة {bulkResults.errors.length} طالب
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