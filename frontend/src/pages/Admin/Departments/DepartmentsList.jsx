import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { getDepartments, deleteDepartment } from "../../../services/api";

export default function DepartmentsList() {
  const [items, setItems] = useState([]);
  useEffect(() => { fetch(); }, []);
  const fetch = async () => { const data = await getDepartments(); setItems(data.data || []); };
  const handleDelete = async (id) => { if (confirm("حذف؟")) { await deleteDepartment(id); fetch(); } };
  return (
    <div>
      <div className="page-header"><h1>الأقسام</h1><Link to="/admin/departments/create" className="btn-primary">+ إضافة قسم</Link></div>
      <table className="data-table"><thead><tr><th>#</th><th>الاسم</th><th>إجراءات</th></tr></thead>
      <tbody>{items.map(item => (<tr key={item.id}><td>{item.id}</td><td>{item.name}</td>
      <td><Link to={`/admin/departments/edit/${item.id}`} className="btn-sm">تعديل</Link><button onClick={() => handleDelete(item.id)} className="btn-sm danger">حذف</button></td></tr>))}</tbody></table>
    </div>
  );
}