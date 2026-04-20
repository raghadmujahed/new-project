import { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import {
  getAnnouncement,
  createAnnouncement,
  updateAnnouncement
} from "../../../services/api";

export default function AnnouncementForm() {
  const { id } = useParams();
  const navigate = useNavigate();

  const [loading, setLoading] = useState(false);

  const [form, setForm] = useState({
    title: "",
    content: "",
    target_type: "all",
    target_ids: [],
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
            target_type: data.target_type || "all",
            target_ids: data.target_ids || [],
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

    if (errors[e.target.name]) {
      setErrors({ ...errors, [e.target.name]: null });
    }
  };

  // 🔥 handle target ids (comma separated input)
  const handleTargetIdsChange = (e) => {
    const value = e.target.value;

    const ids = value
      .split(",")
      .map((id) => id.trim())
      .filter((id) => id !== "")
      .map(Number);

    setForm({ ...form, target_ids: ids });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setLoading(true);
    setErrors({});

    try {
      const payload = {
        title: form.title,
        content: form.content,
        target_type: form.target_type,
        target_ids: form.target_type === "all" ? null : form.target_ids,
      };

      if (id) {
        await updateAnnouncement(id, payload);
      } else {
        await createAnnouncement(payload);
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
        <button
          onClick={() => navigate("/admin/announcements")}
          className="btn-secondary"
        >
          رجوع
        </button>
      </div>

      <form onSubmit={handleSubmit} className="form">

        {/* Title */}
        <div className="form-group">
          <label>العنوان *</label>
          <input
            type="text"
            name="title"
            value={form.title}
            onChange={handleChange}
            required
          />
          {errors.title && <span className="error">{errors.title[0]}</span>}
        </div>

        {/* Content */}
        <div className="form-group">
          <label>المحتوى *</label>
          <textarea
            name="content"
            rows="6"
            value={form.content}
            onChange={handleChange}
            required
          />
          {errors.content && <span className="error">{errors.content[0]}</span>}
        </div>

        {/* Target Type */}
        <div className="form-group">
          <label>نوع الاستهداف *</label>
          <select
            name="target_type"
            value={form.target_type}
            onChange={handleChange}
          >
            <option value="all">الجميع</option>
            <option value="role">أدوار</option>
            <option value="user">مستخدمين</option>
            <option value="department">أقسام</option>
          </select>
        </div>

        {/* Target IDs */}
        {form.target_type !== "all" && (
          <div className="form-group">
            <label>
              IDs المستهدفين (افصلي بينهم بفاصلة ,)
            </label>

            <input
              type="text"
              placeholder="مثال: 1,2,3"
              value={form.target_ids.join(",")}
              onChange={handleTargetIdsChange}
            />
          </div>
        )}

        {/* Actions */}
        <div className="form-actions">
          <button
            type="submit"
            className="btn-primary"
            disabled={loading}
          >
            {loading ? "جاري الحفظ..." : (id ? "تحديث" : "إضافة")}
          </button>

          <button
            type="button"
            onClick={() => navigate("/admin/announcements")}
            className="btn-secondary"
          >
            إلغاء
          </button>
        </div>

      </form>
    </div>
  );
}