import { useEffect, useState } from "react";
import { Link } from "react-router-dom";
import { getAnnouncements, deleteAnnouncement } from "../../../services/api";

export default function AnnouncementsList() {
  const [announcements, setAnnouncements] = useState([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    fetchAnnouncements();
  }, []);

  const fetchAnnouncements = async () => {
    setLoading(true);
    try {
      const response = await getAnnouncements();
      setAnnouncements(response.data || []);
    } catch (err) {
      setError("فشل تحميل الإعلانات");
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  const handleDelete = async (id) => {
    if (window.confirm("هل أنت متأكد من حذف هذا الإعلان؟")) {
      try {
        await deleteAnnouncement(id);
        fetchAnnouncements();
      } catch (err) {
        alert("حدث خطأ أثناء الحذف");
      }
    }
  };

  if (loading) return <div className="text-center">جاري التحميل...</div>;
  if (error) return <div className="text-danger">{error}</div>;

  return (
    <div className="announcements-list">
      <div className="page-header">
        <h1>إدارة الإعلانات</h1>
        <Link to="/admin/announcements/create" className="btn-primary">+ إضافة إعلان</Link>
      </div>

      <table className="data-table">
        <thead>
          <tr>
            <th>العنوان</th>
            <th>المحتوى</th>
            <th>تاريخ النشر</th>
            <th>الإجراءات</th>
          </tr>
        </thead>
        <tbody>
          {announcements.map(announcement => (
            <tr key={announcement.id}>
              <td>{announcement.title}</td>
              <td>{announcement.content?.substring(0, 100)}...</td>
              <td>{new Date(announcement.created_at).toLocaleDateString()}</td>
              <td>
                <Link to={`/admin/announcements/edit/${announcement.id}`} className="btn-sm">تعديل</Link>
                <button onClick={() => handleDelete(announcement.id)} className="btn-sm danger">حذف</button>
              </td>
            </tr>
          ))}
          {announcements.length === 0 && (
            <tr><td colSpan="4" className="text-center">لا توجد إعلانات</td></tr>
          )}
        </tbody>
      </table>
    </div>
  );
}