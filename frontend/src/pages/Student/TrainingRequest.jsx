import { useEffect, useState } from "react";
import { useNavigate } from "react-router-dom";

export default function TrainingRequest() {
  const [isEnabled, setIsEnabled] = useState(false);
  const [loading, setLoading] = useState(true);
  const [formData, setFormData] = useState({
    directorate: "",
    school: "",
  });
  const navigate = useNavigate();

useEffect(() => {
  const refreshUser = async () => {
    try {
      const res = await fetch('/api/user', {
        headers: { 'Authorization': 'Bearer ' + localStorage.getItem('access_token') }
      });
      const freshUser = await res.json();
      localStorage.setItem('user', JSON.stringify(freshUser));
      const enabled = freshUser.features?.["training_requests.create"] === 1;
      setIsEnabled(enabled);
    } catch (err) { console.error(err); }
    setLoading(false);
  };
  refreshUser();
}, []);

  const handleChange = (e) => {
    setFormData((prev) => ({ ...prev, [e.target.name]: e.target.value }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log("Training Request:", formData);
    // TODO: استدعاء API لحفظ الطلب
  };

  if (loading) return <div className="text-center">جاري التحقق...</div>;

  if (!isEnabled) {
    return (
      <div className="content-header">
        <h1 className="page-title">طلب التدريب</h1>
        <div className="alert alert-danger mt-3">
          ⛔ عذراً، خدمة تقديم طلبات التدريب مغلقة حالياً. يرجى مراجعة الإدارة.
        </div>
        <button className="btn-secondary" onClick={() => navigate(-1)}>رجوع</button>
      </div>
    );
  }

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">طلب التدريب</h1>
        <p className="page-subtitle">
          قم بتعبئة بيانات طلب التدريب وإرساله للإدارة
        </p>
      </div>
      <div className="section-card">
        <form onSubmit={handleSubmit}>
          <div className="row g-3">
            <div className="col-md-6">
              <label className="form-label-custom">المديرية</label>
              <select
                name="directorate"
                value={formData.directorate}
                onChange={handleChange}
                className="form-select-custom"
              >
                <option value="">اختر المديرية</option>
                <option value="education">مديرية التربية والتعليم</option>
                <option value="health">مديرية الصحة</option>
              </select>
            </div>
            <div className="col-md-6">
              <label className="form-label-custom">المدرسة / جهة التدريب</label>
              <select
                name="school"
                value={formData.school}
                onChange={handleChange}
                className="form-select-custom"
              >
                <option value="">اختر جهة التدريب</option>
                <option value="school-1">مدرسة الحسين الثانوية</option>
                <option value="school-2">مدرسة الملك خالد</option>
              </select>
            </div>
          </div>
          <div className="mt-3">
            <button type="submit" className="btn-primary-custom">
              إرسال الطلب
            </button>
          </div>
        </form>
      </div>
    </>
  );
}