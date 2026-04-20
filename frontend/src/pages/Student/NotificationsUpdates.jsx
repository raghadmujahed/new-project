import { useState } from "react";

export default function NotificationsUpdates() {
  const [notifications] = useState([
    {
      id: 1,
      title: "تم قبول طلب التدريب",
      message: "تمت الموافقة على طلب التدريب الخاص بك.",
      date: "2026-04-08",
    },
    {
      id: 2,
      title: "تحديث على الجدول",
      message: "تم تعديل جدول التدريب لهذا الأسبوع.",
      date: "2026-04-07",
    },
    {
      id: 3,
      title: "تذكير بالتكليف",
      message: "يرجى تسليم التكليف قبل الموعد النهائي.",
      date: "2026-04-05",
    },
  ]);

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">الإشعارات والتحديثات</h1>
        <p className="page-subtitle">
          متابعة آخر الإشعارات والتحديثات المتعلقة بالتدريب
        </p>
      </div>

      <div className="row g-4">
        {notifications.map((item) => (
          <div className="col-12" key={item.id}>
            <div className="panel">
              <div className="d-flex justify-content-between align-items-start flex-wrap gap-2">
                <div>
                  <h5 className="mb-2">{item.title}</h5>
                  <p className="text-muted mb-2">{item.message}</p>
                </div>

                <div>
                  <small className="text-muted">{item.date}</small>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </>
  );
}