import { useState } from "react";

const MentorAssignment = () => {
  const mentors = [
    "أ. محمد خالد",
    "أ. سمر الجعبري",
    "أ. أحمد القواسمي",
    "أ. هناء الطروة",
  ];

  const [students, setStudents] = useState([
    {
      id: 1,
      studentName: "محمد أحمد النجار",
      specialization: "أساليب تدريس اللغة العربية",
      status: "قيد المراجعة",
      mentor: "",
      notes: "",
    },
    {
      id: 2,
      studentName: "آية خالد أبو عيشة",
      specialization: "الإرشاد النفسي والتربوي",
      status: "قيد المراجعة",
      mentor: "",
      notes: "",
    },
    {
      id: 3,
      studentName: "لينا محمود الطروة",
      specialization: "أساليب تدريس الرياضيات",
      status: "بانتظار تعيين مرشد",
      mentor: "",
      notes: "",
    },
  ]);

  const handleMentorChange = (id, value) => {
    setStudents((prev) =>
      prev.map((student) =>
        student.id === id ? { ...student, mentor: value } : student
      )
    );
  };

  const handleNotesChange = (id, value) => {
    setStudents((prev) =>
      prev.map((student) =>
        student.id === id ? { ...student, notes: value } : student
      )
    );
  };

  const handleApprove = (id) => {
    setStudents((prev) =>
      prev.map((student) =>
        student.id === id
          ? {
              ...student,
              status: student.mentor ? "مقبول" : "بانتظار تعيين مرشد",
            }
          : student
      )
    );
  };

  const handleReject = (id) => {
    setStudents((prev) =>
      prev.map((student) =>
        student.id === id ? { ...student, status: "مرفوض" } : student
      )
    );
  };

  const getStatusClass = (status) => {
    switch (status) {
      case "مقبول":
        return "badge-custom badge-success";
      case "مرفوض":
        return "badge-custom badge-danger";
      case "بانتظار تعيين مرشد":
        return "badge-custom badge-warning";
      default:
        return "badge-custom badge-info";
    }
  };

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">تعيين المعلم المرشد</h1>
        <p className="page-subtitle">
          مراجعة طلبات الطلبة، قبولهم أو رفضهم، وتعيين المعلم المرشد لكل طالب.
        </p>
      </div>

      <div className="section-card">
        <h4>الطلبة بانتظار التعيين والمراجعة</h4>

        <div className="table-wrapper">
          <table className="table-custom">
            <thead>
              <tr>
                <th>اسم الطالب</th>
                <th>التخصص</th>
                <th>الحالة</th>
                <th>المعلم المرشد</th>
                <th>ملاحظات</th>
                <th>الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              {students.map((student) => (
                <tr key={student.id}>
                  <td className="fw-bold">{student.studentName}</td>
                  <td>{student.specialization}</td>

                  <td>
                    <span className={getStatusClass(student.status)}>
                      {student.status}
                    </span>
                  </td>

                  <td style={{ minWidth: "220px" }}>
                    <select
                      value={student.mentor}
                      onChange={(e) =>
                        handleMentorChange(student.id, e.target.value)
                      }
                      className="form-select-custom"
                    >
                      <option value="">اختر المعلم المرشد</option>
                      {mentors.map((mentor) => (
                        <option key={mentor} value={mentor}>
                          {mentor}
                        </option>
                      ))}
                    </select>
                  </td>

                  <td style={{ minWidth: "220px" }}>
                    <textarea
                      value={student.notes}
                      onChange={(e) =>
                        handleNotesChange(student.id, e.target.value)
                      }
                      placeholder="اكتب ملاحظات"
                      className="form-textarea-custom"
                    />
                  </td>

                  <td>
                    <div className="table-actions">
                      <button
                        type="button"
                        onClick={() => handleApprove(student.id)}
                        className="btn-success-custom btn-sm-custom"
                      >
                        قبول
                      </button>

                      <button
                        type="button"
                        onClick={() => handleReject(student.id)}
                        className="btn-danger-custom btn-sm-custom"
                      >
                        رفض
                      </button>
                    </div>
                  </td>
                </tr>
              ))}

              {students.length === 0 && (
                <tr>
                  <td colSpan="6" className="text-center">
                    لا توجد طلبات حالية بانتظار التعيين
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
};

export default MentorAssignment;