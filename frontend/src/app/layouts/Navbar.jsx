export default function Navbar({ onMenuClick }) {
  const savedUser = JSON.parse(localStorage.getItem("user")) || {};

  const roleLabels = {
    admin: "مدير النظام",
    coordinator: "المنسق الأكاديمي",
    supervisor: "المشرف الأكاديمي",
    mentor: "المشرف الميداني",
    principal: "مدير جهة التدريب",
    health_directorate: "مديرية الصحة",
    education_directorate: "مديرية التربية والتعليم",
    student: "الطالب المتدرب",
  };

  const userName = savedUser?.name || "مستخدم النظام";
  const roleName = roleLabels[savedUser?.role] || "بوابة النظام";

  return (
    <header className="top-navbar">
      <div className="navbar-right">
        <button className="mobile-menu-btn" onClick={onMenuClick}>
          ☰
        </button>

        <div>
          <h3 className="navbar-title">نظام إدارة التدريب الميداني</h3>
          <p className="navbar-subtitle">
            منصة أكاديمية لمتابعة التدريب العملي والتربوي
          </p>
        </div>
      </div>

      <div className="navbar-left">
        <div className="navbar-chip">
          <span>{userName}</span>
          <span>—</span>
          <span>{roleName}</span>
        </div>
      </div>
    </header>
  );
}