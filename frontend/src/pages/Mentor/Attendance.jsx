import { useState } from "react";
import PageHeader from "../../components/common/PageHeader";
import EmptyState from "../../components/common/EmptyState";

export default function Attendance() {
  const [records, setRecords] = useState([
    {
      id: 1,
      studentName: "أحمد محمد",
      date: "2026-04-06",
      status: "حاضر",
    },
    {
      id: 2,
      studentName: "سارة خالد",
      date: "2026-04-06",
      status: "غائب",
    },
    {
      id: 3,
      studentName: "محمد يوسف",
      date: "2026-04-06",
      status: "متأخر",
    },
  ]);

  const handleStatusChange = (id, newStatus) => {
    setRecords((prev) =>
      prev.map((record) =>
        record.id === id ? { ...record, status: newStatus } : record
      )
    );
  };

  return (
    <>
      <PageHeader
        title="الحضور والغياب"
        subtitle="توثيق حضور وغياب الطلبة بشكل يومي"
      />

      {!records.length ? (
        <EmptyState
          title="لا توجد سجلات حضور"
          description="لم يتم تسجيل أي حضور أو غياب حتى الآن."
        />
      ) : (
        <div className="list-clean">
          {records.map((record) => (
            <div key={record.id} className="list-item-card">
              <div className="panel-header">
                <div>
                  <h4 className="panel-title">{record.studentName}</h4>
                  <p className="panel-subtitle">التاريخ: {record.date}</p>
                </div>

                <span
                  className={`badge-custom ${
                    record.status === "حاضر"
                      ? "badge-success"
                      : record.status === "غائب"
                      ? "badge-danger"
                      : "badge-warning"
                  }`}
                >
                  {record.status}
                </span>
              </div>

              <div className="table-actions" style={{ marginTop: "12px" }}>
                <button
                  className="btn-light-custom btn-sm-custom"
                  onClick={() => handleStatusChange(record.id, "حاضر")}
                >
                  حاضر
                </button>
                <button
                  className="btn-light-custom btn-sm-custom"
                  onClick={() => handleStatusChange(record.id, "غائب")}
                >
                  غائب
                </button>
                <button
                  className="btn-light-custom btn-sm-custom"
                  onClick={() => handleStatusChange(record.id, "متأخر")}
                >
                  متأخر
                </button>
              </div>
            </div>
          ))}
        </div>
      )}
    </>
  );
}