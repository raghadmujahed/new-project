import { useEffect, useState } from "react";
import { getDashboardStats, getRecentActivities, getLatestAnnouncement } from "../../services/api"; // سنضيف هذه الدوال لاحقاً

export default function AdminDashboard() {
  const [stats, setStats] = useState({
    total_students: 0,
    total_sites: 0,
    completed_evaluations: 0,
    pending_reports: 0,
  });
  const [activities, setActivities] = useState([]);
  const [announcement, setAnnouncement] = useState(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState("");

  useEffect(() => {
    const fetchData = async () => {
      try {
        const statsData = await getDashboardStats();
        // ضبط الإحصائيات حسب الحقول المتوقعة من الـ API
        setStats({
          total_students: statsData.total_students || 0,
          total_sites: statsData.total_sites || 0,
          completed_evaluations: statsData.completed_evaluations || 0,
          pending_reports: statsData.pending_evaluations || 0, // أو أي حقل مناسب
        });

        // جلب آخر الأنشطة (يمكن استدعاء endpoint منفصل)
        // const activitiesData = await getRecentActivities();
        // setActivities(activitiesData);

        // جلب آخر إعلان
        // const announcementData = await getLatestAnnouncement();
        // setAnnouncement(announcementData);
      } catch (err) {
        console.error(err);
        setError("حدث خطأ أثناء تحميل البيانات");
      } finally {
        setLoading(false);
      }
    };
    fetchData();
  }, []);

  if (loading) return <div className="loading">جاري التحميل...</div>;
  if (error) return <div className="error">{error}</div>;

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">لوحة تحكم مدير النظام</h1>
        <p className="page-subtitle">
          نظرة عامة على عمليات التدريب الميداني والأنشطة داخل النظام
        </p>
      </div>

      <div className="dashboard-grid">
        <div className="stat-card primary">
          <div className="stat-title">إجمالي الطلبة</div>
          <div className="stat-value">{stats.total_students}</div>
        </div>

        <div className="stat-card accent">
          <div className="stat-title">أماكن التدريب</div>
          <div className="stat-value">{stats.total_sites}</div>
        </div>

        <div className="stat-card success">
          <div className="stat-title">التقييمات المكتملة</div>
          <div className="stat-value">{stats.completed_evaluations}</div>
        </div>

        <div className="stat-card info">
          <div className="stat-title">التقارير المعلقة</div>
          <div className="stat-value">{stats.pending_reports}</div>
        </div>
      </div>

      <div className="dashboard-row">
        <div className="section-card">
          <h4>أحدث الأنشطة</h4>
          {activities.length === 0 ? (
            <p>لا توجد أنشطة حديثة.</p>
          ) : (
            <div className="activity-list">
              {activities.map((activity, idx) => (
                <div key={idx} className="activity-item">
                  <h6>{activity.title}</h6>
                  <p>{activity.description}</p>
                </div>
              ))}
            </div>
          )}
        </div>

        <div className="announcement-box">
          <h5>إعلان</h5>
          {announcement ? (
            <p>{announcement.content}</p>
          ) : (
            <p>لا توجد إعلانات حالية.</p>
          )}
        </div>
      </div>
    </>
  );
}