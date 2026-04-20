import { useState } from "react";
import PageHeader from "../../components/common/PageHeader";
import EmptyState from "../../components/common/EmptyState";

export default function TrainingLog() {
  const [logs, setLogs] = useState([
    {
      id: 1,
      date: "2026-04-06",
      title: "تنفيذ حصة صفية",
      description: "تم تنفيذ حصة في مادة اللغة العربية للصف السابع.",
      status: "قيد المراجعة",
    },
    {
      id: 2,
      date: "2026-04-07",
      title: "ملاحظة صفية",
      description: "تمت ملاحظة أداء المعلم داخل الصف وتوثيق الملاحظات.",
      status: "مقبول",
    },
  ]);

  const handleDelete = (logId) => {
    const confirmed = window.confirm("هل تريد حذف هذا السجل؟");
    if (!confirmed) return;

    setLogs((prev) => prev.filter((log) => log.id !== logId));
  };

  return (
    <>
      <PageHeader
        title="سجل التدريب اليومي"
        subtitle="إضافة ومتابعة السجلات اليومية الخاصة بالتدريب الميداني"
      />

      <div className="page-actions">
        <button className="btn-primary-custom">إضافة سجل جديد</button>
      </div>

      {!logs.length ? (
        <EmptyState
          title="لا توجد سجلات يومية"
          description="لم يتم إدخال أي سجل تدريب يومي حتى الآن."
        />
      ) : (
        <div className="list-clean">
          {logs.map((log) => (
            <div key={log.id} className="list-item-card">
              <div className="panel-header">
                <div>
                  <h4 className="panel-title">{log.title}</h4>
                  <p className="panel-subtitle">{log.description}</p>
                </div>

                <span
                  className={`badge-custom ${
                    log.status === "مقبول"
                      ? "badge-success"
                      : log.status === "قيد المراجعة"
                      ? "badge-warning"
                      : "badge-danger"
                  }`}
                >
                  {log.status}
                </span>
              </div>

              <div className="page-actions" style={{ marginTop: "12px" }}>
                <span className="text-soft">التاريخ: {log.date}</span>

                <div className="table-actions">
                  <button className="btn-light-custom btn-sm-custom">
                    تعديل
                  </button>
                  <button className="btn-light-custom btn-sm-custom">
                    إرسال للمراجعة
                  </button>
                  <button
                    className="btn-danger-custom btn-sm-custom"
                    onClick={() => handleDelete(log.id)}
                  >
                    حذف
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </>
  );
}