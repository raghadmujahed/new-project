import { useState } from "react";

export default function Assignments() {
  const [assignments, setAssignments] = useState([
    {
      id: 1,
      title: "إعداد خطة درس",
      description: "قم بإعداد خطة درس كاملة وتسليمها بصيغة PDF.",
      dueDate: "2026-04-15",
      status: "قيد التنفيذ",
      file: null,
    },
    {
      id: 2,
      title: "تقرير ملاحظة صفية",
      description: "اكتب تقريرًا حول الحصة الصفية التي تمت ملاحظتها.",
      dueDate: "2026-04-20",
      status: "غير مرفوع",
      file: null,
    },
  ]);

  const handleFileChange = (id, file) => {
    setAssignments((prev) =>
      prev.map((item) =>
        item.id === id
          ? {
              ...item,
              file,
              status: file ? "تم الرفع" : item.status,
            }
          : item
      )
    );
  };

  const handleSubmit = (id) => {
    const target = assignments.find((item) => item.id === id);
    console.log("Submitted assignment:", target);
  };

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">التكليفات</h1>
        <p className="page-subtitle">
          متابعة التكليفات المطلوبة ورفع الملفات الخاصة بها
        </p>
      </div>

      <div className="row g-4">
        {assignments.map((assignment) => (
          <div className="col-12" key={assignment.id}>
            <div className="panel">
              <div className="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-3">
                <div>
                  <h5 className="mb-2">{assignment.title}</h5>
                  <p className="text-muted mb-2">{assignment.description}</p>
                  <p className="mb-1">
                    <strong>آخر موعد:</strong> {assignment.dueDate}
                  </p>
                  <p className="mb-0">
                    <strong>الحالة:</strong> {assignment.status}
                  </p>
                </div>
              </div>

              <div className="row g-3 align-items-end">
                <div className="col-md-8">
                  <label className="form-label">رفع ملف التكليف</label>
                  <input
                    type="file"
                    className="form-control-custom"
                    onChange={(e) =>
                      handleFileChange(assignment.id, e.target.files[0])
                    }
                  />
                  {assignment.file && (
                    <small className="text-muted d-block mt-2">
                      الملف المختار: {assignment.file.name}
                    </small>
                  )}
                </div>

                <div className="col-md-4">
                  <button
                    type="button"
                    className="btn-primary-custom"
                    onClick={() => handleSubmit(assignment.id)}
                  >
                    تسليم التكليف
                  </button>
                </div>
              </div>
            </div>
          </div>
        ))}
      </div>
    </>
  );
}