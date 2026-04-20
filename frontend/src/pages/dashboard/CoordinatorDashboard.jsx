export default function CoordinatorDashboard() {
  return (
    <>
      <div className="content-header">
        <h1 className="page-title">لوحة تحكم منسق التدريب</h1>
        <p className="page-subtitle">
          متابعة توزيع الطلبة والتنسيق مع جهات التدريب والإشراف الأكاديمي
        </p>
      </div>

      <div className="dashboard-grid">
        <div className="stat-card primary">
          <div className="stat-title">الطلبة الموزعون</div>
          <div className="stat-value">186</div>
        </div>

        <div className="stat-card accent">
          <div className="stat-title">جهات التدريب</div>
          <div className="stat-value">24</div>
        </div>

        <div className="stat-card success">
          <div className="stat-title">الزيارات المنفذة</div>
          <div className="stat-value">41</div>
        </div>

        <div className="stat-card info">
          <div className="stat-title">الطلبات الجديدة</div>
          <div className="stat-value">9</div>
        </div>
      </div>

      <div className="dashboard-row">
        <div className="section-card">
          <h4>أحدث الأنشطة</h4>

          <div className="activity-list">
            <div className="activity-item">
              <h6>تم اعتماد دفعة توزيع جديدة</h6>
              <p>تم توزيع مجموعة من الطلبة على مؤسسات التدريب المعتمدة.</p>
            </div>

            <div className="activity-item">
              <h6>استلام تقرير متابعة ميدانية</h6>
              <p>تم رفع تقرير جديد من أحد المشرفين الأكاديميين.</p>
            </div>
          </div>
        </div>

        <div className="announcement-box">
          <h5>إعلان</h5>
          <p>
            يرجى مراجعة قوائم الطلبة والتأكد من اكتمال بيانات جهات التدريب لهذا الفصل.
          </p>
        </div>
      </div>
    </>
  );
}