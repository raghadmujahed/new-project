import { useState } from "react";

const Profile = () => {
  const [profileData, setProfileData] = useState({
    principalName: "أ. أحمد محمد الجعبري",
    schoolName: "مدرسة الحسين الثانوية",
    directorate: "مديرية الخليل",
    schoolType: "مدرسة حكومية",
    phone: "0599000000",
    email: "principal@school.edu",
    address: "الخليل - عين سارة",
    username: "principal_hs",
  });

  const [savedMessage, setSavedMessage] = useState("");

  const handleChange = (e) => {
    const { name, value } = e.target;
    setProfileData((prev) => ({
      ...prev,
      [name]: value,
    }));
    setSavedMessage("");
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setSavedMessage("تم حفظ التعديلات بنجاح.");
  };

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">الملف الشخصي</h1>
        <p className="page-subtitle">
          تعديل بيانات مدير المدرسة وبيانات المدرسة.
        </p>
      </div>

      <div className="section-card">
        <h4>بيانات المدير والمدرسة</h4>

        <form onSubmit={handleSubmit}>
          <div className="row g-3">
            <div className="col-md-6">
              <label className="form-label-custom">اسم المدير</label>
              <input
                type="text"
                name="principalName"
                value={profileData.principalName}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">اسم المدرسة</label>
              <input
                type="text"
                name="schoolName"
                value={profileData.schoolName}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">مديرية التربية</label>
              <select
                name="directorate"
                value={profileData.directorate}
                onChange={handleChange}
                className="form-select-custom"
              >
                <option value="مديرية الخليل">مديرية الخليل</option>
                <option value="مديرية يطا">مديرية يطا</option>
                <option value="مديرية جنوب الخليل">مديرية جنوب الخليل</option>
                <option value="مديرية شمال الخليل">مديرية شمال الخليل</option>
              </select>
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">نوع المدرسة</label>
              <input
                type="text"
                name="schoolType"
                value={profileData.schoolType}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">رقم الهاتف</label>
              <input
                type="text"
                name="phone"
                value={profileData.phone}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">البريد الإلكتروني</label>
              <input
                type="email"
                name="email"
                value={profileData.email}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">العنوان</label>
              <input
                type="text"
                name="address"
                value={profileData.address}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">اسم المستخدم</label>
              <input
                type="text"
                name="username"
                value={profileData.username}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>
          </div>

          {savedMessage && (
            <div className="alert-custom alert-success mt-3">
              {savedMessage}
            </div>
          )}

          <div className="mt-3">
            <button type="submit" className="btn-primary-custom">
              حفظ التعديلات
            </button>
          </div>
        </form>
      </div>
    </>
  );
};

export default Profile;