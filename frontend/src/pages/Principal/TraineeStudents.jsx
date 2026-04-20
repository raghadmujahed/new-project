import { useState } from "react";

const studentsData = [
  {
    id: 1,
    name: "محمد أحمد النجار",
    universityNumber: "22001111",
    college: "كلية التربية",
    specialization: "أساليب تدريس اللغة العربية",
    directorate: "مديرية الخليل",
    schoolName: "مدرسة الحسين الثانوية",
    schoolAddress: "الخليل - عين سارة",
  },
  {
    id: 2,
    name: "آية خالد أبو عيشة",
    universityNumber: "22002222",
    college: "كلية التربية",
    specialization: "الإرشاد النفسي والتربوي",
    directorate: "مديرية شمال الخليل",
    schoolName: "مدرسة حلحول الثانوية",
    schoolAddress: "حلحول",
  },
  {
    id: 3,
    name: "لينا محمود الطروة",
    universityNumber: "22003333",
    college: "كلية التربية",
    specialization: "أساليب تدريس الرياضيات",
    directorate: "مديرية جنوب الخليل",
    schoolName: "مدرسة دير سامت الثانوية",
    schoolAddress: "دير سامت",
  },
];

const evaluationItems = [
  "الدوام",
  "التعاون مع الهيئة التدريسية",
  "العلاقة مع الطلبة",
  "تحركاته في الاتجاه نحو المهنة",
  "المقدرة على التخطيط مع الهيئة التدريسية",
  "دراسة تحضيراته اليومية",
];

const TraineeStudents = () => {
  const [selectedStudentId, setSelectedStudentId] = useState(studentsData[0].id);
  const [savedMessage, setSavedMessage] = useState("");

  const [evaluation, setEvaluation] = useState({
    "الدوام": { notes: "" },
    "التعاون مع الهيئة التدريسية": { notes: "" },
    "العلاقة مع الطلبة": { notes: "" },
    "تحركاته في الاتجاه نحو المهنة": { notes: "" },
    "المقدرة على التخطيط مع الهيئة التدريسية": { notes: "" },
    "دراسة تحضيراته اليومية": { notes: "" },
    generalNotes: "",
  });

  const selectedStudent =
    studentsData.find((student) => student.id === selectedStudentId) ||
    studentsData[0];

  const handleStudentChange = (e) => {
    setSelectedStudentId(Number(e.target.value));
    setSavedMessage("");
  };

  const handleNotesChange = (item, value) => {
    setEvaluation((prev) => ({
      ...prev,
      [item]: {
        ...prev[item],
        notes: value,
      },
    }));
    setSavedMessage("");
  };

  const handleGeneralNotesChange = (value) => {
    setEvaluation((prev) => ({
      ...prev,
      generalNotes: value,
    }));
    setSavedMessage("");
  };

  const handleSubmit = (e) => {
    e.preventDefault();
    setSavedMessage("تم حفظ تقييم الطالب بنجاح.");
  };

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">الطلبة المتدربون وتقييمهم</h1>
        <p className="page-subtitle">
          عرض بيانات الطلبة المتدربين داخل المدرسة وتعبئة نموذج التقييم.
        </p>
      </div>

      <div className="section-card mb-3">
        <h4>اختيار الطالب المتدرب</h4>

        <div className="row">
          <div className="col-md-6">
            <label className="form-label-custom">اختر الطالب المتدرب</label>
            <select
              value={selectedStudentId}
              onChange={handleStudentChange}
              className="form-select-custom"
            >
              {studentsData.map((student) => (
                <option key={student.id} value={student.id}>
                  {student.name}
                </option>
              ))}
            </select>
          </div>
        </div>
      </div>

      <div className="section-card mb-3">
        <h4>بيانات الطالب</h4>

        <div className="summary-grid">
          <div className="kpi-box">
            <strong>{selectedStudent.name}</strong>
            <span>اسم الطالب</span>
          </div>

          <div className="kpi-box">
            <strong>{selectedStudent.universityNumber}</strong>
            <span>الرقم الجامعي</span>
          </div>

          <div className="kpi-box">
            <strong>{selectedStudent.college}</strong>
            <span>الكلية</span>
          </div>

          <div className="kpi-box">
            <strong>{selectedStudent.specialization}</strong>
            <span>التخصص التربوي</span>
          </div>

          <div className="kpi-box">
            <strong>{selectedStudent.directorate}</strong>
            <span>اسم المديرية</span>
          </div>

          <div className="kpi-box">
            <strong>{selectedStudent.schoolName}</strong>
            <span>اسم المدرسة</span>
          </div>

          <div className="kpi-box">
            <strong>{selectedStudent.schoolAddress}</strong>
            <span>عنوان المدرسة</span>
          </div>
        </div>
      </div>

      <div className="section-card">
        <h4>نموذج التقييم</h4>

        <form onSubmit={handleSubmit}>
          <div className="table-wrapper">
            <table className="table-custom">
              <thead>
                <tr>
                  <th>البند</th>
                  <th>الملاحظات</th>
                </tr>
              </thead>
              <tbody>
                {evaluationItems.map((item) => (
                  <tr key={item}>
                    <td className="fw-bold">{item}</td>
                    <td style={{ minWidth: "300px" }}>
                      <textarea
                        value={evaluation[item].notes}
                        onChange={(e) =>
                          handleNotesChange(item, e.target.value)
                        }
                        placeholder="اكتب الملاحظات"
                        className="form-textarea-custom"
                      />
                    </td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>

          <div className="mt-3">
            <label className="form-label-custom">ملاحظات عامة</label>
            <textarea
              value={evaluation.generalNotes}
              onChange={(e) => handleGeneralNotesChange(e.target.value)}
              placeholder="اكتب ملاحظات عامة حول أداء الطالب"
              className="form-textarea-custom"
            />
          </div>

          {savedMessage && (
            <div className="alert-custom alert-success mt-3">
              {savedMessage}
            </div>
          )}

          <div className="mt-3">
            <button type="submit" className="btn-primary-custom">
              حفظ التقييم
            </button>
          </div>
        </form>
      </div>
    </>
  );
};

export default TraineeStudents;