import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { getCourse, createCourse, updateCourse } from "../../../services/api";

export default function CourseForm() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [form, setForm] = useState({ code: "", name: "", description: "", credit_hours: 3, type: "practical" });
  useEffect(() => { if (id) getCourse(id).then(data => setForm(data)); }, [id]);
  const handleSubmit = async (e) => {
    e.preventDefault();
    if (id) await updateCourse(id, form);
    else await createCourse(form);
    navigate("/admin/courses");
  };
  return (
    <form onSubmit={handleSubmit} className="form">
      <h1>{id ? "تعديل مساق" : "إضافة مساق"}</h1>
      <div className="form-group"><label>الكود</label><input type="text" value={form.code} onChange={e => setForm({...form, code: e.target.value})} required /></div>
      <div className="form-group"><label>الاسم</label><input type="text" value={form.name} onChange={e => setForm({...form, name: e.target.value})} required /></div>
      <div className="form-group"><label>الوصف</label><textarea value={form.description} onChange={e => setForm({...form, description: e.target.value})} /></div>
      <div className="form-group"><label>عدد الساعات</label><input type="number" value={form.credit_hours} onChange={e => setForm({...form, credit_hours: parseInt(e.target.value)})} /></div>
      <div className="form-group"><label>النوع</label>
        <select value={form.type} onChange={e => setForm({...form, type: e.target.value})}>
          <option value="practical">عملي</option><option value="theoretical">نظري</option><option value="both">نظري وعملي</option>
        </select>
      </div>
      <button type="submit">حفظ</button>
    </form>
  );
}