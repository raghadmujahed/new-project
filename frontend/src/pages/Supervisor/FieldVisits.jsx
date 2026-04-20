import { useState } from "react";
import PageHeader from "../../components/common/PageHeader";
import EmptyState from "../../components/common/EmptyState";

const initialForm = {
  studentName: "",
  place: "",
  date: "",
  status: "مجدولة",
  report: "",
};

export default function FieldVisits() {
  const [visits, setVisits] = useState([
    {
      id: 1,
      studentName: "أحمد محمد",
      place: "مدرسة الحسين الثانوية",
      date: "2026-04-12",
      status: "مجدولة",
      report: "",
    },
    {
      id: 2,
      studentName: "سارة خالد",
      place: "مدرسة بنات الخليل",
      date: "2026-04-15",
      status: "تمت",
      report: "تمت الزيارة ومراجعة أداء الطالبة داخل الصف.",
    },
    {
      id: 3,
      studentName: "محمد يوسف",
      place: "مركز الإرشاد النفسي",
      date: "2026-04-18",
      status: "قيد المتابعة",
      report: "",
    },
  ]);

  const [form, setForm] = useState(initialForm);
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [editingVisitId, setEditingVisitId] = useState(null);

  const openAddForm = () => {
    setForm(initialForm);
    setEditingVisitId(null);
    setIsFormOpen(true);
  };

  const openEditForm = (visit) => {
    setForm({
      studentName: visit.studentName,
      place: visit.place,
      date: visit.date,
      status: visit.status,
      report: visit.report || "",
    });
    setEditingVisitId(visit.id);
    setIsFormOpen(true);
  };

  const openReportForm = (visit) => {
    setForm({
      studentName: visit.studentName,
      place: visit.place,
      date: visit.date,
      status: visit.status,
      report: visit.report || "",
    });
    setEditingVisitId(visit.id);
    setIsFormOpen(true);
  };

  const closeForm = () => {
    setForm(initialForm);
    setEditingVisitId(null);
    setIsFormOpen(false);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;

    setForm((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!form.studentName.trim() || !form.place.trim() || !form.date) {
      alert("يرجى تعبئة اسم الطالب ومكان التدريب وتاريخ الزيارة");
      return;
    }

    if (editingVisitId) {
      setVisits((prev) =>
        prev.map((visit) =>
          visit.id === editingVisitId ? { ...visit, ...form } : visit
        )
      );
      alert("تم تحديث بيانات الزيارة بنجاح");
    } else {
      const newVisit = {
        id: Date.now(),
        ...form,
      };

      setVisits((prev) => [newVisit, ...prev]);
      alert("تمت جدولة الزيارة بنجاح");
    }

    closeForm();
  };

  const handleDelete = (visitId) => {
    const confirmed = window.confirm("هل تريد حذف هذه الزيارة؟");
    if (!confirmed) return;

    setVisits((prev) => prev.filter((visit) => visit.id !== visitId));
  };

  const getBadgeClass = (status) => {
    if (status === "تمت") return "badge-success";
    if (status === "قيد المتابعة") return "badge-warning";
    return "badge-primary";
  };

  return (
    <>
      <PageHeader
        title="الزيارات الميدانية"
        subtitle="جدولة الزيارات الميدانية ومتابعة حالتها وتقاريرها"
      />

      <div className="page-actions">
        <button className="btn-primary-custom" onClick={openAddForm}>
          جدولة زيارة جديدة
        </button>
      </div>

      {isFormOpen && (
        <div className="form-card mb-3">
          <form onSubmit={handleSubmit}>
            <div className="page-grid two-cols">
              <div className="form-group-custom">
                <label className="form-label-custom">اسم الطالب</label>
                <input
                  type="text"
                  name="studentName"
                  className="form-control-custom"
                  value={form.studentName}
                  onChange={handleChange}
                />
              </div>

              <div className="form-group-custom">
                <label className="form-label-custom">مكان التدريب</label>
                <input
                  type="text"
                  name="place"
                  className="form-control-custom"
                  value={form.place}
                  onChange={handleChange}
                />
              </div>
            </div>

            <div className="page-grid two-cols">
              <div className="form-group-custom">
                <label className="form-label-custom">تاريخ الزيارة</label>
                <input
                  type="date"
                  name="date"
                  className="form-control-custom"
                  value={form.date}
                  onChange={handleChange}
                />
              </div>

              <div className="form-group-custom">
                <label className="form-label-custom">الحالة</label>
                <select
                  name="status"
                  className="form-select-custom"
                  value={form.status}
                  onChange={handleChange}
                >
                  <option value="مجدولة">مجدولة</option>
                  <option value="قيد المتابعة">قيد المتابعة</option>
                  <option value="تمت">تمت</option>
                </select>
              </div>
            </div>

            <div className="form-group-custom">
              <label className="form-label-custom">تقرير الزيارة</label>
              <textarea
                name="report"
                className="form-textarea-custom"
                value={form.report}
                onChange={handleChange}
                placeholder="اكتب تقريرًا موجزًا عن الزيارة الميدانية..."
              />
            </div>

            <div className="table-actions">
              <button type="submit" className="btn-primary-custom">
                {editingVisitId ? "حفظ التعديلات" : "حفظ الزيارة"}
              </button>

              <button
                type="button"
                className="btn-light-custom"
                onClick={closeForm}
              >
                إلغاء
              </button>
            </div>
          </form>
        </div>
      )}

      {!visits.length ? (
        <EmptyState
          title="لا توجد زيارات ميدانية"
          description="لم يتم جدولة أي زيارة حتى الآن."
        />
      ) : (
        <div className="list-clean">
          {visits.map((visit) => (
            <div key={visit.id} className="list-item-card">
              <div className="panel-header">
                <div>
                  <h4 className="panel-title">{visit.studentName}</h4>
                  <p className="panel-subtitle">{visit.place}</p>
                </div>

                <span className={`badge-custom ${getBadgeClass(visit.status)}`}>
                  {visit.status}
                </span>
              </div>

              <div className="page-actions" style={{ marginTop: "12px" }}>
                <span className="text-soft">تاريخ الزيارة: {visit.date}</span>

                <div className="table-actions">
                  <button
                    className="btn-light-custom btn-sm-custom"
                    onClick={() => openEditForm(visit)}
                  >
                    تعديل
                  </button>

                  <button
                    className="btn-light-custom btn-sm-custom"
                    onClick={() => openReportForm(visit)}
                  >
                    رفع تقرير
                  </button>

                  <button
                    className="btn-danger-custom btn-sm-custom"
                    onClick={() => handleDelete(visit.id)}
                  >
                    حذف
                  </button>
                </div>
              </div>

              {visit.report ? (
                <div className="alert-custom alert-info" style={{ marginTop: "12px" }}>
                  <strong>تقرير الزيارة:</strong>
                  <div>{visit.report}</div>
                </div>
              ) : null}
            </div>
          ))}
        </div>
      )}
    </>
  );
}