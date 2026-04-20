import { useState } from "react";

export default function TrainingRequest() {
  const [formData, setFormData] = useState({
    directorate: "",
    school: "",
  });

  const handleChange = (e) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value,
    });
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    console.log(formData);
  };

  return (
    <>
      {/* Header */}
      <div className="content-header">
        <h1 className="page-title">طلب التدريب</h1>
        <p className="page-subtitle">
          قم باختيار المديرية والمدرسة لإرسال طلب التدريب
        </p>
      </div>

      {/* Form */}
      <div className="panel">
        <form onSubmit={handleSubmit} className="row g-3">

          {/* Directorate */}
          <div className="col-md-6">
            <label className="form-label">المديرية</label>
            <select
              name="directorate"
              value={formData.directorate}
              onChange={handleChange}
              className="form-control-custom"
            >
              <option value="">اختر المديرية</option>
              <option value="north">مديرية الشمال</option>
              <option value="south">مديرية الجنوب</option>
            </select>
          </div>

          {/* School */}
          <div className="col-md-6">
            <label className="form-label">المدرسة</label>
            <select
              name="school"
              value={formData.school}
              onChange={handleChange}
              className="form-control-custom"
            >
              <option value="">اختر المدرسة</option>
              <option value="school1">مدرسة 1</option>
              <option value="school2">مدرسة 2</option>
            </select>
          </div>

          {/* Submit */}
          <div className="col-12 mt-3">
            <button type="submit" className="btn-primary-custom">
              إرسال الطلب
            </button>
          </div>

        </form>
      </div>
    </>
  );
}