import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { getCourses, deleteCourse } from "../../../services/api";

export default function CoursesList() {
  const [courses, setCourses] = useState([]);
  useEffect(() => { fetchCourses(); }, []);
  const fetchCourses = async () => { const data = await getCourses(); setCourses(data.data || []); };
  const handleDelete = async (id) => { if (confirm("حذف المساق؟")) { await deleteCourse(id); fetchCourses(); } };
  return (
    <div>
      <div className="page-header"><h1>المساقات</h1><Link to="/admin/courses/create" className="btn-primary">+ إضافة مساق</Link></div>
      <table className="data-table">
        <thead><tr><th>الكود</th><th>الاسم</th><th>الساعات</th><th>النوع</th><th>إجراءات</th></tr></thead>
        <tbody>{courses.map(c => (
          <tr key={c.id}><td>{c.code}</td><td>{c.name}</td><td>{c.credit_hours}</td><td>{c.type_label}</td>
          <td><Link to={`/admin/courses/edit/${c.id}`} className="btn-sm">تعديل</Link><button onClick={() => handleDelete(c.id)} className="btn-sm danger">حذف</button></td></tr>
        ))}</tbody>
      </table>
    </div>
  );
}