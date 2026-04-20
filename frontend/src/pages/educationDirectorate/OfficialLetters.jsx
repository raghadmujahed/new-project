import { useState } from "react";

const OfficialLetters = () => {
  const [savedMessage, setSavedMessage] = useState("");

  const [letters, setLetters] = useState([
    {
      id: 1,
      title: "كتاب توزيع الطلبة المتدربين",
      receiver: "مدارس مديرية الخليل",
      date: "2026-03-28",
      status: "بانتظار الموافقة",
      notes: "",
    },
    {
      id: 2,
      title: "كتاب اعتماد أماكن التدريب",
      receiver: "مدارس المرحلة الثانوية",
      date: "2026-03-27",
      status: "تمت الموافقة",
      notes: "تمت المراجعة من قبل مسؤول المديرية.",
    },
    {
      id: 3,
      title: "كتاب تحديث الطاقة الاستيعابية",
      receiver: "جميع المدارس",
      date: "2026-03-26",
      status: "تم الإرسال",
      notes: "",
    },
    {
      id: 4,
      title: "كتاب إعادة اعتماد المدارس المستقبلة للطلبة",
      receiver: "مدارس مديرية الخليل",
      date: "2026-03-25",
      status: "مؤرشف",
      notes: "تم حفظ الكتاب في الأرشيف بعد التنفيذ.",
    },
  ]);

  const handleStatusChange = (id, value) => {
    setLetters((prev) =>
      prev.map((letter) =>
        letter.id === id ? { ...letter, status: value } : letter
      )
    );
    setSavedMessage("");
  };

  const handleNotesChange = (id, value) => {
    setLetters((prev) =>
      prev.map((letter) =>
        letter.id === id ? { ...letter, notes: value } : letter
      )
    );
    setSavedMessage("");
  };

  const handleSave = () => {
    setSavedMessage("تم حفظ التعديلات على الكتب الرسمية بنجاح.");
  };

  const getBadgeClass = (status) => {
    if (status === "تمت الموافقة") return "badge-custom badge-success";
    if (status === "تم الإرسال") return "badge-custom badge-info";
    if (status === "مؤرشف") return "badge-custom badge-soft";
    return "badge-custom badge-warning";
  };

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">الكتب الرسمية</h1>
        <p className="page-subtitle">
          متابعة الكتب الرسمية، اعتمادها، تحديث حالتها، وإرسالها إلى المدارس.
        </p>
      </div>

      <div className="section-card">
        <h4>إدارة الكتب الرسمية</h4>

        <div className="table-wrapper">
          <table className="table-custom">
            <thead>
              <tr>
                <th>عنوان الكتاب</th>
                <th>الجهة المستلمة</th>
                <th>التاريخ</th>
                <th>الحالة الحالية</th>
                <th>تعديل الحالة</th>
                <th>الملاحظات</th>
              </tr>
            </thead>
            <tbody>
              {letters.map((letter) => (
                <tr key={letter.id}>
                  <td>{letter.title}</td>
                  <td>{letter.receiver}</td>
                  <td>{letter.date}</td>
                  <td>
                    <span className={getBadgeClass(letter.status)}>
                      {letter.status}
                    </span>
                  </td>
                  <td>
                    <select
                      className="form-select-custom"
                      value={letter.status}
                      onChange={(e) =>
                        handleStatusChange(letter.id, e.target.value)
                      }
                    >
                      <option value="بانتظار الموافقة">بانتظار الموافقة</option>
                      <option value="تمت الموافقة">تمت الموافقة</option>
                      <option value="تم الإرسال">تم الإرسال</option>
                      <option value="مؤرشف">مؤرشف</option>
                    </select>
                  </td>
                  <td>
                    <textarea
                      className="form-textarea-custom"
                      value={letter.notes}
                      onChange={(e) =>
                        handleNotesChange(letter.id, e.target.value)
                      }
                      placeholder="اكتب ملاحظات حول الكتاب"
                    />
                  </td>
                </tr>
              ))}

              {letters.length === 0 && (
                <tr>
                  <td colSpan="6" className="text-center">
                    لا توجد كتب رسمية مسجلة حاليًا
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>

        <div className="mt-3">
          <button
            type="button"
            className="btn-primary-custom"
            onClick={handleSave}
          >
            حفظ التعديلات
          </button>
        </div>

        {savedMessage && (
          <div className="alert-custom alert-success mt-3">
            {savedMessage}
          </div>
        )}
      </div>
    </>
  );
};

export default OfficialLetters;