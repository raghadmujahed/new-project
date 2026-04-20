import { useState } from "react";
import PageHeader from "../../components/common/PageHeader";

export default function ChangePassword() {
  const [form, setForm] = useState({
    currentPassword: "",
    newPassword: "",
    confirmPassword: "",
  });

  const handleChange = (e) => {
    setForm((prev) => ({
      ...prev,
      [e.target.name]: e.target.value,
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!form.currentPassword || !form.newPassword || !form.confirmPassword) {
      alert("يرجى تعبئة جميع الحقول");
      return;
    }

    if (form.newPassword !== form.confirmPassword) {
      alert("كلمة المرور الجديدة وتأكيدها غير متطابقين");
      return;
    }

    alert("تم تغيير كلمة المرور بنجاح");

    setForm({
      currentPassword: "",
      newPassword: "",
      confirmPassword: "",
    });
  };

  return (
    <>
      <PageHeader
        title="تغيير كلمة المرور"
        subtitle="قم بإدخال كلمة المرور الحالية ثم الجديدة"
      />

      <div className="form-card">
        <form onSubmit={handleSubmit}>
          <div className="form-group-custom">
            <label className="form-label-custom">كلمة المرور الحالية</label>
            <input
              type="password"
              name="currentPassword"
              className="form-control-custom"
              value={form.currentPassword}
              onChange={handleChange}
            />
          </div>

          <div className="form-group-custom">
            <label className="form-label-custom">كلمة المرور الجديدة</label>
            <input
              type="password"
              name="newPassword"
              className="form-control-custom"
              value={form.newPassword}
              onChange={handleChange}
            />
          </div>

          <div className="form-group-custom">
            <label className="form-label-custom">تأكيد كلمة المرور الجديدة</label>
            <input
              type="password"
              name="confirmPassword"
              className="form-control-custom"
              value={form.confirmPassword}
              onChange={handleChange}
            />
          </div>

          <button type="submit" className="btn-primary-custom">
            تحديث كلمة المرور
          </button>
        </form>
      </div>
    </>
  );
}