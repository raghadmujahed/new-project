import { useState } from "react";
import PageHeader from "../../components/common/PageHeader";
import EmptyState from "../../components/common/EmptyState";

export default function AttendanceFollowUp() {
  const [records] = useState([
    {
      id: 1,
      studentName: "أحمد محمد",
      section: "شعبة التربية العملية 1",
      trainingPlace: "مدرسة الحسين الثانوية",
      date: "2026-04-06",
      status: "حاضر",
    },
    {
      id: 2,
      studentName: "سارة خالد",
      section: "شعبة التربية العملية 2",
      trainingPlace: "مدرسة بنات الخليل",
      date: "2026-04-06",
      status: "غائب",
    },
    {
      id: 3,
      studentName: "محمد يوسف",
      section: "شعبة التربية العملية 1",
      trainingPlace: "مركز الإرشاد النفسي",
      date: "2026-04-06",
      status: "متأخر",
    },
  ]);

  const getBadgeClass = (status) => {
    if (status === "حاضر") return "badge-success";
    if (status === "غائب") return "badge-danger";
    return "badge-warning";
  };

  return (
    <>
      <PageHeader
        title="متابعة الحضور والغياب"
        subtitle="عرض حالة حضور الطلبة في أماكن التدريب دون تعديل السجل"
      />

      {!records.length ? (
        <EmptyState
          title="لا توجد سجلات حضور"
          description="لم يتم تسجيل حضور أو غياب للطلبة حتى الآن."
        />
      ) : (
        <div className="list-clean">
          {records.map((item) => (
            <div key={item.id} className="list-item-card">
              <div className="panel-header">
                <div>
                  <h4 className="panel-title">{item.studentName}</h4>
                  <p className="panel-subtitle">{item.section}</p>
                </div>

                <span className={`badge-custom ${getBadgeClass(item.status)}`}>
                  {item.status}
                </span>
              </div>

              <div
                className="list-clean"
                style={{ marginTop: "12px", gap: "6px" }}
              >
                <span className="text-soft">
                  مكان التدريب: {item.trainingPlace}
                </span>
                <span className="text-soft">التاريخ: {item.date}</span>
              </div>
            </div>
          ))}
        </div>
      )}
    </>
  );
}