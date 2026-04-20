import { useState } from "react";
import PageHeader from "../../components/common/PageHeader";
import EmptyState from "../../components/common/EmptyState";

export default function Evaluations() {
  const [evaluations, setEvaluations] = useState([
    {
      id: 1,
      studentName: "أحمد محمد",
      type: "تقييم أكاديمي",
      score: 88,
      status: "مكتمل",
    },
    {
      id: 2,
      studentName: "سارة خالد",
      type: "تقييم زيارة ميدانية",
      score: 91,
      status: "مكتمل",
    },
    {
      id: 3,
      studentName: "محمد يوسف",
      type: "تقييم سجل يومي",
      score: null,
      status: "قيد الإدخال",
    },
  ]);

  const handleDelete = (id) => {
    const confirmed = window.confirm("هل تريد حذف هذا التقييم؟");
    if (!confirmed) return;

    setEvaluations((prev) => prev.filter((item) => item.id !== id));
  };

  return (
    <>
      <PageHeader
        title="التقييمات"
        subtitle="إدخال ومتابعة تقييمات الطلبة الأكاديمية والميدانية"
      />

      <div className="page-actions">
        <button className="btn-primary-custom">إضافة تقييم جديد</button>
      </div>

      {!evaluations.length ? (
        <EmptyState
          title="لا توجد تقييمات"
          description="لم يتم إضافة أي تقييم حتى الآن."
        />
      ) : (
        <div className="list-clean">
          {evaluations.map((item) => (
            <div key={item.id} className="list-item-card">
              <div className="panel-header">
                <div>
                  <h4 className="panel-title">{item.studentName}</h4>
                  <p className="panel-subtitle">{item.type}</p>
                </div>

                <span
                  className={`badge-custom ${
                    item.status === "مكتمل" ? "badge-success" : "badge-warning"
                  }`}
                >
                  {item.status}
                </span>
              </div>

              <div className="page-actions" style={{ marginTop: "12px" }}>
                <span className="text-soft">
                  العلامة: {item.score !== null ? item.score : "غير مدخلة"}
                </span>

                <div className="table-actions">
                  <button className="btn-light-custom btn-sm-custom">
                    تعديل
                  </button>
                  <button className="btn-light-custom btn-sm-custom">
                    عرض التفاصيل
                  </button>
                  <button
                    className="btn-danger-custom btn-sm-custom"
                    onClick={() => handleDelete(item.id)}
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