import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { getAnnouncement, createAnnouncement, updateAnnouncement } from "../../../services/api";

export default function AnnouncementForm() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [loading, setLoading] = useState(false);
  const [form, setForm] = useState({
    title: "",
    content: "",
  });
  const [errors, setErrors] = useState({});

  useEffect(() => {
    if (id) {
      const fetchAnnouncement = async () => {
        try {
          const data = await getAnnouncement(id);
          setForm({
            title: data.title,
            content: data.content,
          });
        } catch (error) {
          console.error(error);
        }
      };
      fetchAnnouncement();
    }
  }, [id]);

  const handleChange = (e) => {
    setForm({ ...form, [e.target.name]: e.target.value });
    if (errors[e.target.name]) setErrors({ ...errors, [e.target.name]: null });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    try {
      if (id) {
        await updateAnnouncement(id, form);
      } else {
        await createAnnouncement(form);
      }
      navigate("/admin/announcements");
    } catch (err) {
      if (err.response?.data?.errors) {
        setErrors(err.response.data.errors);
      } else {
        alert("حدث خطأ أثناء حفظ الإعلان");
      }
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="announcement-form">
      <div className="page-header">
        <h1>{id ? "تعديل إعلان" : "إضافة إعلان جديد"}</h1>
        <button onClick={() => navigate("/admin/announcements")} className="btn-secondary">رجوع</button>
      </div>

      <form onSubmit={handleSubmit} className="form">
        <div className="form-group">
          <label>العنوان *</label>
          <input type="text" name="title" value={form.title} onChange={handleChange} required />
          {errors.title && <span className="error">{errors.title[0]}</span>}
        </div>

        <div className="form-group">
          <label>المحتوى *</label>
          <textarea name="content" rows="6" value={form.content} onChange={handleChange} required />
          {errors.content && <span className="error">{errors.content[0]}</span>}
        </div>

        <div className="form-actions">
          <button type="submit" className="btn-primary" disabled={loading}>
            {loading ? "جاري الحفظ..." : (id ? "تحديث" : "إضافة")}
          </button>
          <button type="button" onClick={() => navigate("/admin/announcements")} className="btn-secondary">إلغاء</button>
        </div>
      </form>
    </div>
  );
}