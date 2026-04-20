import { useMemo, useState } from "react";

export default function Schedule() {
  const trainingInfo = {
    directorate: "مديرية الخليل",
    school: "مدرسة الحسين الثانوية",
    trainingType: "تدريب مدرسي",
    supervisor: "سيتم تحديده من قبل الإدارة",
    requestStatus: "قيد المعالجة",
  };

  const trainingPeriod = {
    periodName: "الفصل الأول 2025 / 2026",
    startDate: "2025-10-01",
    endDate: "2025-12-31",
    requiredDays: 40,
    requiredHours: 120,
  };

  const [trainingProgram, setTrainingProgram] = useState([
    {
      id: 1,
      day: "الأحد",
      date: "2025-10-06",
      time: "08:00 - 10:00",
      activity: "ملاحظة صفية",
      place: "الصف العاشر",
      notes: "متابعة أسلوب إدارة الصف",
      status: "حضور",
      hours: 2,
    },
    {
      id: 2,
      day: "الثلاثاء",
      date: "2025-10-08",
      time: "09:00 - 11:00",
      activity: "مشاركة في شرح الدرس",
      place: "الصف التاسع",
      notes: "تم تقديم جزء من الحصة بإشراف المعلم",
      status: "حضور",
      hours: 2,
    },
  ]);

  const [formData, setFormData] = useState({
    day: "",
    date: "",
    time: "",
    activity: "",
    place: "",
    notes: "",
    status: "حضور",
    hours: "",
  });

  const completedDays = useMemo(
    () => trainingProgram.filter((item) => item.status === "حضور").length,
    [trainingProgram]
  );

  const absentDays = useMemo(
    () => trainingProgram.filter((item) => item.status === "غياب").length,
    [trainingProgram]
  );

  const completedHours = useMemo(
    () =>
      trainingProgram
        .filter((item) => item.status === "حضور")
        .reduce((sum, item) => sum + Number(item.hours || 0), 0),
    [trainingProgram]
  );

  const progressDays = useMemo(() => {
    return Math.min(
      100,
      Math.round((completedDays / trainingPeriod.requiredDays) * 100)
    );
  }, [completedDays, trainingPeriod.requiredDays]);

  const progressHours = useMemo(() => {
    return Math.min(
      100,
      Math.round((completedHours / trainingPeriod.requiredHours) * 100)
    );
  }, [completedHours, trainingPeriod.requiredHours]);

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleAddProgramItem = (e) => {
    e.preventDefault();

    if (
      !formData.day ||
      !formData.date ||
      !formData.time ||
      !formData.activity ||
      !formData.place ||
      !formData.hours
    ) {
      return;
    }

    const newItem = {
      id: Date.now(),
      day: formData.day,
      date: formData.date,
      time: formData.time,
      activity: formData.activity,
      place: formData.place,
      notes: formData.notes,
      status: formData.status,
      hours: Number(formData.hours),
    };

    setTrainingProgram((prev) => [...prev, newItem]);

    setFormData({
      day: "",
      date: "",
      time: "",
      activity: "",
      place: "",
      notes: "",
      status: "حضور",
      hours: "",
    });
  };

  const handleDeleteItem = (id) => {
    setTrainingProgram((prev) => prev.filter((item) => item.id !== id));
  };

  const getStatusBadgeClass = (status) => {
    return status === "حضور" ? "badge-success" : "badge-danger";
  };

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">برنامج التدريب</h1>
        <p className="page-subtitle">
          متابعة برنامج التدريب، الحضور والغياب، والساعات المنجزة
        </p>
      </div>

      <div className="dashboard-grid mb-3">
        <div className="stat-card primary">
          <div className="stat-title">أيام الحضور</div>
          <div className="stat-value">{completedDays}</div>
          <div className="stat-meta">
            من أصل {trainingPeriod.requiredDays} يوم مطلوب
          </div>
        </div>

        <div className="stat-card danger">
          <div className="stat-title">أيام الغياب</div>
          <div className="stat-value">{absentDays}</div>
          <div className="stat-meta">الأيام المسجلة كغياب</div>
        </div>

        <div className="stat-card success">
          <div className="stat-title">الساعات المنجزة</div>
          <div className="stat-value">{completedHours}</div>
          <div className="stat-meta">
            من أصل {trainingPeriod.requiredHours} ساعة
          </div>
        </div>

        <div className="stat-card accent">
          <div className="stat-title">حالة الطلب</div>
          <div className="stat-value" style={{ fontSize: "1.3rem" }}>
            {trainingInfo.requestStatus}
          </div>
          <div className="stat-meta">{trainingInfo.school}</div>
        </div>
      </div>

      <div className="dashboard-row">
        <div className="section-card">
          <div className="panel-header">
            <div>
              <h3 className="panel-title">معلومات التدريب</h3>
              <p className="panel-subtitle">
                بيانات جهة التدريب والفترة الزمنية المعتمدة
              </p>
            </div>
          </div>

          <div className="page-grid two-cols">
            <div className="mini-panel">
              <strong>المديرية</strong>
              <span>{trainingInfo.directorate}</span>
            </div>

            <div className="mini-panel">
              <strong>المدرسة / المركز</strong>
              <span>{trainingInfo.school}</span>
            </div>

            <div className="mini-panel">
              <strong>نوع التدريب</strong>
              <span>{trainingInfo.trainingType}</span>
            </div>

            <div className="mini-panel">
              <strong>المشرف</strong>
              <span>{trainingInfo.supervisor}</span>
            </div>

            <div className="mini-panel">
              <strong>الفترة</strong>
              <span>{trainingPeriod.periodName}</span>
            </div>

            <div className="mini-panel">
              <strong>تاريخ البداية - النهاية</strong>
              <span>
                {trainingPeriod.startDate} - {trainingPeriod.endDate}
              </span>
            </div>
          </div>
        </div>

        <div className="announcement-box">
          <h5>نسبة الإنجاز</h5>

          <p className="mb-2">تقدم الأيام</p>
          <div className="progress-custom mb-2">
            <div
              className="progress-bar-custom"
              style={{ width: `${progressDays}%` }}
            />
          </div>
          <p className="text-muted mb-3">{progressDays}% مكتمل</p>

          <p className="mb-2">تقدم الساعات</p>
          <div className="progress-custom mb-2">
            <div
              className="progress-bar-custom"
              style={{ width: `${progressHours}%` }}
            />
          </div>
          <p className="text-muted mb-0">{progressHours}% مكتمل</p>
        </div>
      </div>

      <div className="section-card mb-3">
        <div className="panel-header">
          <div>
            <h3 className="panel-title">إضافة يوم تدريب</h3>
            <p className="panel-subtitle">
              قم بإدخال تفاصيل اليوم التدريبي ليظهر مباشرة في السجل
            </p>
          </div>
        </div>

        <form onSubmit={handleAddProgramItem}>
          <div className="row g-3">
            <div className="col-md-6 col-lg-3">
              <label className="form-label-custom">اليوم</label>
              <select
                name="day"
                value={formData.day}
                onChange={handleChange}
                className="form-select-custom"
              >
                <option value="">اختر اليوم</option>
                <option value="الأحد">الأحد</option>
                <option value="الإثنين">الإثنين</option>
                <option value="الثلاثاء">الثلاثاء</option>
                <option value="الأربعاء">الأربعاء</option>
                <option value="الخميس">الخميس</option>
              </select>
            </div>

            <div className="col-md-6 col-lg-3">
              <label className="form-label-custom">التاريخ</label>
              <input
                type="date"
                name="date"
                value={formData.date}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6 col-lg-3">
              <label className="form-label-custom">الوقت</label>
              <input
                type="text"
                name="time"
                value={formData.time}
                onChange={handleChange}
                placeholder="مثال: 08:00 - 10:00"
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6 col-lg-3">
              <label className="form-label-custom">عدد الساعات</label>
              <input
                type="number"
                min="1"
                name="hours"
                value={formData.hours}
                onChange={handleChange}
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6 col-lg-3">
              <label className="form-label-custom">النشاط</label>
              <input
                type="text"
                name="activity"
                value={formData.activity}
                onChange={handleChange}
                placeholder="مثال: ملاحظة صفية"
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6 col-lg-3">
              <label className="form-label-custom">المكان</label>
              <input
                type="text"
                name="place"
                value={formData.place}
                onChange={handleChange}
                placeholder="مثال: الصف التاسع"
                className="form-control-custom"
              />
            </div>

            <div className="col-md-6 col-lg-3">
              <label className="form-label-custom">الحالة</label>
              <select
                name="status"
                value={formData.status}
                onChange={handleChange}
                className="form-select-custom"
              >
                <option value="حضور">حضور</option>
                <option value="غياب">غياب</option>
              </select>
            </div>

            <div className="col-md-6 col-lg-3">
              <label className="form-label-custom">ملاحظات</label>
              <input
                type="text"
                name="notes"
                value={formData.notes}
                onChange={handleChange}
                placeholder="اكتب ملاحظات"
                className="form-control-custom"
              />
            </div>
          </div>

          <div className="mt-3">
            <button type="submit" className="btn-primary-custom">
              إضافة إلى البرنامج
            </button>
          </div>
        </form>
      </div>

      <div className="section-card">
        <div className="panel-header">
          <div>
            <h3 className="panel-title">سجل التدريب</h3>
            <p className="panel-subtitle">
              جميع الأيام التدريبية المضافة مع الحالة والساعات
            </p>
          </div>
        </div>

        <div className="table-wrapper">
          <table className="table-custom">
            <thead>
              <tr>
                <th>اليوم</th>
                <th>التاريخ</th>
                <th>الوقت</th>
                <th>النشاط</th>
                <th>المكان</th>
                <th>الساعات</th>
                <th>الحالة</th>
                <th>ملاحظات</th>
                <th>إجراء</th>
              </tr>
            </thead>
            <tbody>
              {trainingProgram.map((item) => (
                <tr key={item.id}>
                  <td>{item.day}</td>
                  <td>{item.date}</td>
                  <td>{item.time}</td>
                  <td>{item.activity}</td>
                  <td>{item.place}</td>
                  <td>{item.hours}</td>
                  <td>
                    <span className={`badge-custom ${getStatusBadgeClass(item.status)}`}>
                      {item.status}
                    </span>
                  </td>
                  <td>{item.notes || "-"}</td>
                  <td>
                    <div className="table-actions">
                      <button
                        type="button"
                        className="btn-danger-custom btn-sm-custom"
                        onClick={() => handleDeleteItem(item.id)}
                      >
                        حذف
                      </button>
                    </div>
                  </td>
                </tr>
              ))}

              {trainingProgram.length === 0 && (
                <tr>
                  <td colSpan="9" className="text-center">
                    لا توجد بيانات مضافة بعد
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>
    </>
  );
}