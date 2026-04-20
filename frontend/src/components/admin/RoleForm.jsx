import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { getUser, createUser, updateUser, getRoles, getDepartments } from "../../services/api";
const roleFieldsMap = {
  student: {
    required: ["university_id", "major"],
    fields: {
      major: "التخصص",
      graduation_year: "سنة التخرج",
      gpa: "المعدل التراكمي",
    },
  },
  teacher: {
    required: ["subject"],
    fields: {
      subject: "المادة الأساسية",
      hire_date: "تاريخ التوظيف",
    },
  },
  counselor: {
    required: ["counseling_field"],
    fields: {
      counseling_field: "مجال الإرشاد",
      license_number: "رقم الترخيص",
    },
  },
  school_manager: {
    required: ["school_name"],
    fields: {
      school_name: "اسم المدرسة",
      management_years: "سنوات الخبرة الإدارية",
    },
  },
};

export default function RoleForm({ roleType, roleName }) {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [roles, setRoles] = useState([]);
  const [departments, setDepartments] = useState([]);
  const [errors, setErrors] = useState({});

  const [form, setForm] = useState({
    university_id: "",
    name: "",
    email: "",
    password: "",
    password_confirmation: "",
    role_id: "",
    department_id: "",
    phone: "",
    status: "active",
  });

  const [extraFields, setExtraFields] = useState({});

  // تحميل الأدوار والأقسام
  useEffect(() => {
    const fetchData = async () => {
      try {
        const [rolesData, deptsData] = await Promise.all([getRoles(), getDepartments()]);
        setRoles(rolesData.data || []);
        setDepartments(deptsData.data || []);
      } catch (err) {
        console.error(err);
      }
    };
    fetchData();
  }, []);

  // تعيين role_id تلقائياً عند تحميل الأدوار
  useEffect(() => {
    if (roles.length > 0 && roleType) {
      const matchedRole = roles.find(role => role.name.toLowerCase().includes(roleType));
      if (matchedRole) {
        setForm(prev => ({ ...prev, role_id: matchedRole.id }));
      }
    }
  }, [roles, roleType]);

  // جلب بيانات المستخدم في حالة التعديل
  useEffect(() => {
    if (id) {
      const fetchUser = async () => {
        try {
          const userData = await getUser(id);
          setForm({
            university_id: userData.university_id || "",
            name: userData.name,
            email: userData.email,
            password: "",
            password_confirmation: "",
            role_id: userData.role_id || "",
            department_id: userData.department_id || "",
            phone: userData.phone || "",
            status: userData.status || "active",
          });
          const extra = {};
          const fields = roleFieldsMap[roleType]?.fields || {};
          Object.keys(fields).forEach(key => {
            extra[key] = userData[key] || "";
          });
          setExtraFields(extra);
        } catch (err) {
          console.error(err);
        }
      };
      fetchUser();
    }
  }, [id, roleType]);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
    if (errors[e.target.name]) setErrors({ ...errors, [e.target.name]: null });
  };

  const handleExtraChange = (e) => {
    setExtraFields({ ...extraFields, [e.target.name]: e.target.value });
    if (errors[e.target.name]) setErrors({ ...errors, [e.target.name]: null });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    const payload = { ...form, ...extraFields };

    try {
      if (id) {
        await updateUser(id, payload);
      } else {
        await createUser(payload);
      }
      navigate("/admin/users");
    } catch (err) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        alert("حدث خطأ أثناء حفظ المستخدم");
      }
    } finally {
      setLoading(false);
    }
  };

  const currentFields = roleFieldsMap[roleType]?.fields || {};
  const requiredFields = roleFieldsMap[roleType]?.required || [];

  return (
    <div className="user-form">
      <div className="page-header">
        <h1>{id ? `تعديل ${roleName}` : `إضافة ${roleName} جديد`}</h1>
        <button onClick={() => navigate("/admin/users")} className="btn-secondary">رجوع</button>
      </div>

      <form onSubmit={handleSubmit} className="form">
        <div className="form-row">
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
        </div>

        <div className="form-row">
          <div className="form-group">
            <label>الرقم الجامعي {requiredFields.includes("university_id") && "*"}</label>
            <input type="text" name="university_id" value={form.university_id} onChange={handleChange} required={requiredFields.includes("university_id")} />
          </div>
          <div className="form-group">
            <label>رقم الهاتف</label>
            <input type="text" name="phone" value={form.phone} onChange={handleChange} />
          </div>
        </div>

        <div className="form-row">
          <div className="form-group">
            <label>القسم</label>
            <select name="department_id" value={form.department_id} onChange={handleChange}>
              <option value="">اختر القسم</option>
              {departments.map(dept => (
                <option key={dept.id} value={dept.id}>{dept.name}</option>
              ))}
            </select>
          </div>
          <div className="form-group">
            <label>الحالة</label>
            <select name="status" value={form.status} onChange={handleChange}>
              <option value="active">نشط</option>
              <option value="inactive">غير نشط</option>
              <option value="suspended">موقوف</option>
            </select>
          </div>
        </div>

        {Object.entries(currentFields).map(([fieldName, label]) => (
          <div className="form-group" key={fieldName}>
            <label>{label} {requiredFields.includes(fieldName) && "*"}</label>
            <input
              type="text"
              name={fieldName}
              value={extraFields[fieldName] || ""}
              onChange={handleExtraChange}
              required={requiredFields.includes(fieldName)}
            />
            {errors[fieldName] && <span className="error">{errors[fieldName][0]}</span>}
          </div>
        ))}

        <div className="form-row">
          <div className="form-group">
            <label>كلمة المرور {!id && "*"}</label>
            <input type="password" name="password" value={form.password} onChange={handleChange} required={!id} />
            {errors.password && <span className="error">{errors.password[0]}</span>}
          </div>
          <div className="form-group">
            <label>تأكيد كلمة المرور {!id && "*"}</label>
            <input type="password" name="password_confirmation" value={form.password_confirmation} onChange={handleChange} required={!id} />
          </div>
        </div>

        <div className="form-actions">
          <button type="submit" className="btn-primary" disabled={loading}>
            {loading ? "جاري الحفظ..." : (id ? "تحديث" : "إضافة")}
          </button>
          <button type="button" onClick={() => navigate("/admin/users")} className="btn-secondary">إلغاء</button>
        </div>
      </form>
    </div>
  );
}