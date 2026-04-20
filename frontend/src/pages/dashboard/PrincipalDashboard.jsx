const PrincipalDashboard = () => {
  const principalInfo = {
    principalName: "أ. أحمد محمد الجعبري",
    schoolName: "مدرسة الحسين الثانوية",
    directorate: "مديرية الخليل",
    schoolType: "مدرسة حكومية",
    phone: "0599000000",
    email: "principal@school.edu",
  };

  const summaryCards = [
    {
      title: "طلبات التدريب الجديدة",
      value: "5",
      desc: "طلبات بحاجة لمراجعة واعتماد",
      className: "warning",
    },
    {
      title: "الطلبة المتدربون",
      value: "12",
      desc: "عدد الطلبة المقبولين داخل المدرسة",
      className: "primary",
    },
    {
      title: "المعلمون المرشدون",
      value: "6",
      desc: "تم تعيينهم لمتابعة الطلبة",
      className: "success",
    },
    {
      title: "الكتب الرسمية",
      value: "3",
      desc: "كتب جديدة واردة من المديرية",
      className: "accent",
    },
  ];

  const pendingRequests = [
    {
      id: 1,
      studentName: "محمد أحمد النجار",
      specialization: "أساليب تدريس اللغة العربية",
      status: "قيد المراجعة",
      badgeClass: "badge-custom badge-warning",
    },
    {
      id: 2,
      studentName: "آية خالد أبو عيشة",
      specialization: "الإرشاد النفسي والتربوي",
      status: "قيد المراجعة",
      badgeClass: "badge-custom badge-warning",
    },
    {
      id: 3,
      studentName: "لينا محمود الطروة",
      specialization: "أساليب تدريس الرياضيات",
      status: "بانتظار تعيين مرشد",
      badgeClass: "badge-custom badge-warning",
    },
  ];

  const latestLetters = [
    {
      id: 1,
      subject: "كتاب اعتماد تدريب طلبة الفصل الأول",
      sender: "مديرية الخليل",
      date: "2026-03-26",
    },
    {
      id: 2,
      subject: "كتاب متابعة توزيع الطلبة",
      sender: "كلية التربية - جامعة الخليل",
      date: "2026-03-25",
    },
  ];

  const latestActivities = [
    "تمت إضافة 3 طلبات تدريب جديدة.",
    "تم اعتماد طالبين للتدريب داخل المدرسة.",
    "تم استلام كتاب رسمي جديد من المديرية.",
    "تم تعيين معلم مرشد جديد لأحد الطلبة.",
  ];

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">الرئيسية - مدير المدرسة</h1>
        <p className="page-subtitle">
          لوحة متابعة طلبات التدريب والمرشدين والكتب الرسمية داخل المدرسة.
        </p>
      </div>

      <div className="section-card mb-3">
        <h4>المعلومات الأساسية</h4>
        <div className="summary-grid">
          <div className="kpi-box">
            <strong>{principalInfo.principalName}</strong>
            <span>اسم المدير</span>
          </div>

          <div className="kpi-box">
            <strong>{principalInfo.schoolName}</strong>
            <span>اسم المدرسة</span>
          </div>

          <div className="kpi-box">
            <strong>{principalInfo.directorate}</strong>
            <span>المديرية</span>
          </div>

          <div className="kpi-box">
            <strong>{principalInfo.schoolType}</strong>
            <span>نوع المدرسة</span>
          </div>

          <div className="kpi-box">
            <strong>{principalInfo.phone}</strong>
            <span>رقم الهاتف</span>
          </div>

          <div className="kpi-box">
            <strong>{principalInfo.email}</strong>
            <span>البريد الإلكتروني</span>
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
        <h4>طلبات التدريب الحديثة</h4>

        <div className="table-wrapper">
          <table className="table-custom">
            <thead>
              <tr>
                <th>اسم الطالب</th>
                <th>التخصص</th>
                <th>الحالة</th>
              </tr>
            </thead>
            <tbody>
              {pendingRequests.map((request) => (
                <tr key={request.id}>
                  <td>{request.studentName}</td>
                  <td>{request.specialization}</td>
                  <td>
                    <span className={request.badgeClass}>{request.status}</span>
                  </td>
                </tr>
              ))}

              {pendingRequests.length === 0 && (
                <tr>
                  <td colSpan="3" className="text-center">
                    لا توجد طلبات تدريب حديثة
                  </td>
                </tr>
              )}
            </tbody>
          </table>
        </div>
      </div>

      <div className="dashboard-row">
        <div className="section-card">
          <h4>آخر الكتب الرسمية</h4>

          <div className="activity-list">
            {latestLetters.map((letter) => (
              <div key={letter.id} className="activity-item">
                <h6>{letter.subject}</h6>
                <p>
                  الجهة المرسلة: {letter.sender}
                  <br />
                  التاريخ: {letter.date}
                </p>
              </div>
            ))}
          </div>
        </div>

        <div className="section-card">
          <h4>آخر الأنشطة والتنبيهات</h4>

          <div className="activity-list">
            {latestActivities.map((activity, index) => (
              <div key={index} className="activity-item">
                <p>{activity}</p>
              </div>
            ))}
          </div>
        </div>
      </div>
    </>
  );
};

export default PrincipalDashboard;