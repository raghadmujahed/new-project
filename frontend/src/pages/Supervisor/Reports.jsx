import PageHeader from "../../components/common/PageHeader";

export default function SupervisorReports() {
  return (
    <>
      <PageHeader
        title="تقارير المشرف الأكاديمي"
        subtitle="عرض ملخصات الأداء والمتابعة والتقارير الخاصة بالطلبة"
      />

      <div className="dashboard-grid">
        <div className="stat-card primary">
          <div className="stat-title">عدد الطلبة تحت الإشراف</div>
          <div className="stat-value">32</div>
        </div>

        <div className="stat-card accent">
          <div className="stat-title">الزيارات المنفذة</div>
          <div className="stat-value">14</div>
        </div>

        <div className="stat-card success">
          <div className="stat-title">التقييمات المكتملة</div>
          <div className="stat-value">27</div>
        </div>

        <div className="stat-card info">
          <div className="stat-title">السجلات قيد المراجعة</div>
          <div className="stat-value">8</div>
        </div>
      </div>

      <div className="dashboard-row">
        <div className="section-card">
          <h4>ملخص المتابعة</h4>

          <div className="activity-list">
            <div className="activity-item">
              <h6>تقرير الحضور والغياب</h6>
              <p>متابعة انتظام الطلبة في أماكن التدريب خلال الأسبوع الحالي.</p>
            </div>

            <div className="activity-item">
              <h6>تقرير الزيارات الميدانية</h6>
              <p>يوضح عدد الزيارات التي تمت والملاحظات المسجلة لكل طالب.</p>
            </div>

            <div className="activity-item">
              <h6>تقرير السجل اليومي</h6>
              <p>مراجعة السجلات اليومية المرسلة من الطلبة وحالتها الحالية.</p>
            </div>
          </div>
        </div>

        <div className="announcement-box">
          <h5>ملاحظة</h5>
          <p>
            سيتم لاحقًا ربط هذه الصفحة بتقارير فعلية قابلة للتصفية والتصدير حسب
            الطالب، الشعبة، والفترة الزمنية.
          </p>
        </div>
      </div>
    </>
  );
}