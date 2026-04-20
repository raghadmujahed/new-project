import { useState } from "react";

const OfficialLetters = () => {
  const [letters, setLetters] = useState([
    {
      id: 1,
      subject: "كتاب اعتماد تدريب طلبة الفصل الأول",
      sender: "مديرية الخليل",
      date: "2026-03-26",
      status: "جديد",
      notes: "",
    },
    {
      id: 2,
      subject: "كتاب متابعة توزيع الطلبة",
      sender: "كلية التربية - جامعة الخليل",
      date: "2026-03-25",
      status: "تمت القراءة",
      notes: "تم الاطلاع وتحويله للمتابعة.",
    },
    {
      id: 3,
      subject: "كتاب ترشيح معلمين مرشدين",
      sender: "مديرية التربية",
      date: "2026-03-24",
      status: "مؤرشف",
      notes: "",
    },
  ]);

  const handleStatusChange = (id, value) => {
    setLetters((prev) =>
      prev.map((letter) =>
        letter.id === id ? { ...letter, status: value } : letter
      )
    );
  };

  const handleNotesChange = (id, value) => {
    setLetters((prev) =>
      prev.map((letter) =>
        letter.id === id ? { ...letter, notes: value } : letter
      )
    );
  };

  const getStatusClass = (status) => {
    switch (status) {
      case "جديد":
        return "badge-custom badge-info";
      case "تمت القراءة":
        return "badge-custom badge-success";
      case "مؤرشف":
        return "badge-custom badge-soft";
      default:
        return "badge-custom badge-warning";
    }
  };

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">الكتب الرسمية</h1>
        <p className="page-subtitle">
          متابعة الكتب الرسمية الواردة من المديرية أو الكلية وإدارة حالتها.
        </p>
      </div>

      <div className="section-card">
        <h4>إدارة الكتب الرسمية</h4>

        <div className="table-wrapper">
          <table className="table-custom">
            <thead>
              <tr>
                <th>عنوان الكتاب</th>
                <th>الجهة المرسلة</th>
                <th>التاريخ</th>
                <th>الحالة</th>
                <th>تعديل الحالة</th>
                <th>ملاحظات</th>
              </tr>
            </thead>
            <tbody>
              {letters.map((letter) => (
                <tr key={letter.id}>
                  <td className="fw-bold">{letter.subject}</td>
                  <td>{letter.sender}</td>
                  <td>{letter.date}</td>

                  <td>
                    <span className={getStatusClass(letter.status)}>
                      {letter.status}
                    </span>
                  </td>

                  <td style={{ minWidth: "180px" }}>
                    <select
                      value={letter.status}
                      onChange={(e) =>
                        handleStatusChange(letter.id, e.target.value)
                      }
                      className="form-select-custom"
                    >
                      <option value="جديد">جديد</option>
                      <option value="تمت القراءة">تمت القراءة</option>
                      <option value="مؤرشف">مؤرشف</option>
                    </select>
                  </td>

                  <td style={{ minWidth: "240px" }}>
                    <textarea
                      value={letter.notes}
                      onChange={(e) =>
                        handleNotesChange(letter.id, e.target.value)
                      }
                      placeholder="اكتب ملاحظاتك حول هذا الكتاب"
                      className="form-textarea-custom"
                    />
                  </td>
                </tr>
              ))}

              {letters.length === 0 && (
                <tr>
                  <td colSpan="6" className="text-center">
                    لا توجد كتب رسمية حاليًا
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

export default OfficialLetters;