export default function SupervisorDashboard() {
  const stats = [
    { title: "عدد الشعب", value: 4, type: "primary", meta: "الشعب المسندة إليك" },
    { title: "المهام النشطة", value: 7, type: "accent", meta: "مهام قيد المتابعة" },
    { title: "الزيارات الميدانية", value: 5, type: "success", meta: "خلال هذا الشهر" },
    { title: "التقييمات المكتملة", value: 18, type: "info", meta: "تم إدخالها بنجاح" },
  ];

  const recentActivities = [
    {
      title: "إضافة مهمة جديدة لشعبة التربية العملية 1",
      description: "تم إنشاء مهمة إعداد خطة درس ومشاركتها مع الطلبة.",
      meta: "منذ ساعتين",
    },
    {
      title: "رفع أحد الطلبة ملف التكليف",
      description: "تم استلام ملف الواجب الخاص بمهمة الزيارة الصفية.",
      meta: "منذ 4 ساعات",
    },
    {
      title: "إدخال تقييم جديد",
      description: "تم اعتماد تقييم أحد الطلبة ضمن استمارة الأداء العملي.",
      meta: "اليوم",
    },
  ];

  const timeline = [
    {
      title: "زيارة ميدانية يوم الأحد",
      description: "متابعة أداء الطلبة في مدرسة الحسين الثانوية.",
    },
    {
      title: "موعد تسليم المهام يوم الإثنين",
      description: "آخر موعد لتسليم مهام خطة الدرس للشعبة الأولى.",
    },
    {
      title: "اجتماع متابعة يوم الأربعاء",
      description: "اجتماع مع المنسق الأكاديمي لمراجعة سير التدريب.",
    },
  ];

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">لوحة تحكم المشرف الأكاديمي</h1>
        <p className="page-subtitle">
          متابعة المهام، الزيارات الميدانية، تقييم الطلبة، والشعب المسندة إليك.
        </p>
      </div>

      <div className="dashboard-grid">
        {stats.map((item, index) => (
          <div key={index} className={`stat-card ${item.type}`}>
            <div>
              <div className="stat-title">{item.title}</div>
              <div className="stat-value">{item.value}</div>
            </div>
            <div className="stat-meta">{item.meta}</div>
          </div>
        ))}
      </div>

      <div className="dashboard-row">
        <div className="section-card">
          <h4>أحدث الأنشطة</h4>

          <div className="activity-list">
            {recentActivities.map((activity, index) => (
              <div key={index} className="activity-item">
                <h6>{activity.title}</h6>
                <p>{activity.description}</p>
                <div className="activity-meta">{activity.meta}</div>
              </div>
            ))}
          </div>
        </div>

        <div className="announcement-box">
          <h5>تنبيه</h5>
          <p>
            يرجى مراجعة المهام غير المسلّمة والتأكد من استكمال التقييمات قبل
            نهاية الأسبوع.
          </p>
        </div>
      </div>

      <div className="dashboard-row">
        <div className="section-card">
          <h4>المهام السريعة</h4>

          <div className="quick-actions-grid">
            <button className="quick-action-btn">إضافة مهمة جديدة</button>
            <button className="quick-action-btn">عرض حلول الطلبة</button>
            <button className="quick-action-btn">إدخال تقييم</button>
            <button className="quick-action-btn">جدولة زيارة ميدانية</button>
          </div>
        </div>

        <div className="section-card">
          <h4>الجدول القادم</h4>

          <div className="timeline-list">
            {timeline.map((item, index) => (
              <div key={index} className="timeline-item">
                <h6>{item.title}</h6>
                <p>{item.description}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </>
  );
}