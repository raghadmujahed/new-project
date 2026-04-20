import { useState } from "react";

export default function TrainingPlaces() {
  const [places, setPlaces] = useState([
    {
      id: 1,
      name: "مدرسة الحسين الثانوية",
      type: "مدرسة حكومية",
      city: "الخليل",
      capacity: 12,
      status: "متاح",
    },
    {
      id: 2,
      name: "مدرسة الملك خالد",
      type: "مدرسة حكومية",
      city: "دورا",
      capacity: 8,
      status: "متاح",
    },
    {
      id: 3,
      name: "مدرسة بنات الخليل",
      type: "مدرسة حكومية",
      city: "الخليل",
      capacity: 0,
      status: "مكتمل",
    },
  ]);

  const [formData, setFormData] = useState({
    name: "",
    type: "",
    city: "",
    capacity: "",
  });

  const getStatusClass = (status) => {
    if (status === "متاح") return "badge-custom badge-success";
    if (status === "مكتمل") return "badge-custom badge-danger";
    return "badge-custom badge-soft";
  };

  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData((prev) => ({
      ...prev,
      [name]: value,
    }));
  };

  const handleAddPlace = (e) => {
    e.preventDefault();

    if (!formData.name || !formData.type || !formData.city || !formData.capacity) {
      return;
    }

    const capacityNumber = Number(formData.capacity);
    const newPlace = {
      id: Date.now(),
      name: formData.name,
      type: formData.type,
      city: formData.city,
      capacity: capacityNumber,
      status: capacityNumber > 0 ? "متاح" : "مكتمل",
    };

    setPlaces((prev) => [newPlace, ...prev]);
    setFormData({
      name: "",
      type: "",
      city: "",
      capacity: "",
    });
  };

  return (
    <>
      <div className="content-header">
        <h1 className="page-title">أماكن التدريب</h1>
        <p className="page-subtitle">
          إدارة وعرض أماكن التدريب المعتمدة التابعة لمديرية التربية والتعليم.
        </p>
      </div>

      <div className="section-card mb-3">
        <h4>إضافة مكان تدريب جديد</h4>

        <form onSubmit={handleAddPlace}>
          <div className="row g-3">
            <div className="col-md-6">
              <label className="form-label-custom">اسم جهة التدريب</label>
              <input
                type="text"
                name="name"
                className="form-control-custom"
                placeholder="أدخل اسم جهة التدريب"
                value={formData.name}
                onChange={handleChange}
              />
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">نوع الجهة</label>
              <select
                name="type"
                className="form-select-custom"
                value={formData.type}
                onChange={handleChange}
              >
                <option value="">اختر النوع</option>
                <option value="مدرسة حكومية">مدرسة حكومية</option>
                <option value="مدرسة خاصة">مدرسة خاصة</option>
                <option value="مركز تعليمي">مركز تعليمي</option>
              </select>
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">المدينة</label>
              <input
                type="text"
                name="city"
                className="form-control-custom"
                placeholder="أدخل اسم المدينة"
                value={formData.city}
                onChange={handleChange}
              />
            </div>

            <div className="col-md-6">
              <label className="form-label-custom">السعة</label>
              <input
                type="number"
                name="capacity"
                className="form-control-custom"
                placeholder="أدخل عدد الطلبة الممكن استقبالهم"
                value={formData.capacity}
                onChange={handleChange}
                min="0"
              />
            </div>
          </div>

          <div className="mt-3">
            <button type="submit" className="btn-primary-custom">
              حفظ مكان التدريب
            </button>
          </div>
        </form>
      </div>

      <div className="section-card">
        <h4>قائمة أماكن التدريب</h4>

        <div className="table-wrapper">
          <table className="table-custom">
            <thead>
              <tr>
                <th>اسم المكان</th>
                <th>النوع</th>
                <th>المدينة</th>
                <th>السعة</th>
                <th>الحالة</th>
                <th>إجراء</th>
              </tr>
            </thead>
            <tbody>
              {places.map((place) => (
                <tr key={place.id}>
                  <td>{place.name}</td>
                  <td>{place.type}</td>
                  <td>{place.city}</td>
                  <td>{place.capacity}</td>
                  <td>
                    <span className={getStatusClass(place.status)}>
                      {place.status}
                    </span>
                  </td>
                  <td>
                    <div className="table-actions">
                      <button
                        type="button"
                        className="btn-outline-custom btn-sm-custom"
                      >
                        عرض
                      </button>
                      <button
                        type="button"
                        className="btn-primary-custom btn-sm-custom"
                      >
                        تعديل
                      </button>
                    </div>
                  </td>
                </tr>
              ))}

              {places.length === 0 && (
                <tr>
                  <td colSpan="6" className="text-center">
                    لا توجد أماكن تدريب مسجلة حاليًا
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