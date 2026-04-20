const EducationDirectorateDashboard = () => {
  const directorateInfo = {
    name: "مديرية الخليل",
    officer: "أ. سامر القواسمي",
    email: "directorate@edu.ps",
    phone: "022222222",
  };

  const summaryCards = [
    {
      title: "الكتب الرسمية الجديدة",
      value: "4",
      desc: "كتب بانتظار المراجعة أو الإرسال",
      className: "primary",
    },
    {
      title: "أماكن التدريب",
      value: "18",
      desc: "عدد المدارس والجهات التدريبية",
      className: "accent",
    },
    {
      title: "الأماكن النشطة",
      value: "14",
      desc: "جهات متاحة لاستقبال الطلبة",
      className: "success",
    },
    {
      title: "بحاجة لتحديث",
      value: "3",
      desc: "مدارس يجب تحديث بياناتها",
      className: "warning",
    },
  ];

  const officialLetters = [
    {
      id: 1,
      title: "كتاب توزيع الطلبة المتدربين",
      receiver: "مدارس مديرية الخليل",
      date: "2026-03-28",
      status: "بانتظار الإرسال",
      badgeClass: "badge-custom badge-warning",
    },
    {
      id: 2,
      title: "كتاب اعتماد أماكن التدريب",
      receiver: "مدارس المرحلة الثانوية",
      date: "2026-03-27",
      status: "تمت الموافقة",
      badgeClass: "badge-custom badge-success",
    },
    {
      id: 3,
      title: "كتاب تحديث الطاقة الاستيعابية",
      receiver: "جميع المدارس",
      date: "2026-03-26",
      status: "تم الإرسال",
      badgeClass: "badge-custom badge-info",
    },
  ];

  const trainingPlaces = [
    {
      id: 1,
      name: "مدرسة الحسين الثانوية",
      type: "مدرسة حكومية",
      capacity: 8,
      contact: "0599000001",
      status: "نشط",
      badgeClass: "badge-custom badge-success",
    },
    {
      id: 2,
      name: "مدرسة ابن رشد",
      type: "مدرسة حكومية",
      capacity: 5,
      contact: "0599000002",
      status: "نشط",
      badgeClass: "badge-custom badge-success",
    },
    {
      id: 3,
      name: "مدرسة خالد بن الوليد",
      type: "مدرسة حكومية",
      capacity: 0,
      contact: "0599000003",
      status: "بحاجة تحديث",
      badgeClass: "badge-custom badge-warning",
    },
  ];

  const latestActivities = [
    "تمت إضافة مكان تدريب جديد في مديرية الخليل.",
    "تم تحديث الطاقة الاستيعابية لمدرسة الحسين الثانوية.",
    "تمت الموافقة على كتاب رسمي جديد.",
    "تم إرسال كتاب رسمي إلى المدارس المعتمدة.",
  ];

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">لوحة مديرية التربية</h1>
        <p className="page-subtitle">
          متابعة الكتب الرسمية، أماكن التدريب، والطاقة الاستيعابية داخل المديرية.
        </p>
      </div>

      <div className="section-card mb-3">
        <h4>المعلومات الأساسية</h4>
        <div className="summary-grid">
          <div className="kpi-box">
            <strong>{directorateInfo.name}</strong>
            <span>اسم المديرية</span>
          </div>

          <div className="kpi-box">
            <strong>{directorateInfo.officer}</strong>
            <span>المسؤول</span>
          </div>

          <div className="kpi-box">
            <strong>{directorateInfo.email}</strong>
            <span>البريد الإلكتروني</span>
          </div>

          <div className="kpi-box">
            <strong>{directorateInfo.phone}</strong>
            <span>رقم الهاتف</span>
          </div>
        </div>
      </div>

      <div className="dashboard-grid mb-3">
        {summaryCards.map((card, index) => (
          <div key={index} className={`stat-card ${card.className}`}>
            <div>
              <div className="stat-title">{card.title}</div>
              <div className="stat-value">{card.value}</div>
            </div>
            <div className="stat-meta">{card.desc}</div>
          </div>
        ))}
      </div>

      <div className="section-card mb-3">
        <h4>الكتب الرسمية</h4>

        <div className="table-wrapper">
          <table className="table-custom">
            <thead>
              <tr>
                <th>عنوان الكتاب</th>
                <th>الجهة المستلمة</th>
                <th>التاريخ</th>
                <th>الحالة</th>
              </tr>
            </thead>
            <tbody>
              {officialLetters.map((letter) => (
                <tr key={letter.id}>
                  <td>{letter.title}</td>
                  <td>{letter.receiver}</td>
                  <td>{letter.date}</td>
                  <td>
                    <span className={letter.badgeClass}>{letter.status}</span>
                  </td>
                </tr>
              ))}

              {officialLetters.length === 0 && (
                <tr>
                  <td colSpan="4" className="text-center">
                    لا توجد كتب رسمية مسجلة حاليًا
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      <div className="section-card mb-3">
        <h4>أماكن التدريب والطاقة الاستيعابية</h4>

        <div className="table-wrapper">
          <table className="table-custom">
            <thead>
              <tr>
                <th>اسم الجهة</th>
                <th>النوع</th>
                <th>الطاقة الاستيعابية</th>
                <th>التواصل</th>
                <th>الحالة</th>
              </tr>
            </thead>
            <tbody>
              {trainingPlaces.map((place) => (
                <tr key={place.id}>
                  <td>{place.name}</td>
                  <td>{place.type}</td>
                  <td>{place.capacity}</td>
                  <td>{place.contact}</td>
                  <td>
                    <span className={place.badgeClass}>{place.status}</span>
                  </td>
                </tr>
              ))}

              {trainingPlaces.length === 0 && (
                <tr>
                  <td colSpan="5" className="text-center">
                    لا توجد أماكن تدريب مسجلة حاليًا
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      <div className="section-card">
        <h4>آخر الأنشطة والتحديثات</h4>

        <div className="activity-list">
          {latestActivities.map((activity, index) => (
            <div key={index} className="activity-item">
              <p>{activity}</p>
            </div>
          ))}
        </div>
      </div>
    </>
  );
};

export default EducationDirectorateDashboard;