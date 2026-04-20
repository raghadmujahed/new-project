import { useState } from "react";
import PageHeader from "../../components/common/PageHeader";
import EmptyState from "../../components/common/EmptyState";
import StatusBadge from "../../components/common/StatusBadge";

export default function Notifications() {
  const [notifications, setNotifications] = useState([
    {
      id: 1,
      title: "تمت إضافة مهمة جديدة",
      message: "قام المشرف الأكاديمي بإضافة مهمة جديدة للطلبة.",
      status: "pending",
    },
    {
      id: 2,
      title: "تم اعتماد تقرير الزيارة",
      message: "تم اعتماد تقرير الزيارة الميدانية بنجاح.",
      status: "approved",
    },
    {
      id: 3,
      title: "تذكير بتعبئة السجل اليومي",
      message: "يرجى تعبئة سجل التدريب اليومي قبل نهاية اليوم.",
      status: "active",
    },
  ]);

  const markAsRead = (id) => {
    setNotifications((prev) => prev.filter((item) => item.id !== id));
  };

  return (
    <>
      <PageHeader
        title="الإشعارات"
        subtitle="هنا تظهر آخر التنبيهات والإشعارات الخاصة بالنظام"
      />

      {!notifications.length ? (
        <EmptyState
          title="لا توجد إشعارات"
          description="لا يوجد أي إشعار جديد في الوقت الحالي."
        />
      ) : (
        <div className="list-clean">
          {notifications.map((item) => (
            <div key={item.id} className="list-item-card">
              <div className="panel-header">
                <div>
                  <h4 className="panel-title">{item.title}</h4>
                  <p className="panel-subtitle">{item.message}</p>
                </div>

                <StatusBadge
                  label={
                    item.status === "pending"
                      ? "جديد"
                      : item.status === "approved"
                      ? "مقروء"
                      : "نشط"
                  }
                  status={item.status}
                />
              </div>

              <div className="page-actions" style={{ marginTop: "12px" }}>
                <button
                  className="btn-light-custom btn-sm-custom"
                  onClick={() => markAsRead(item.id)}
                >
                  تعليم كمقروء
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </>
  );
}