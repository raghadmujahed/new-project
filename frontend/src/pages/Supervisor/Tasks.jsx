import { useMemo, useState } from "react";
import PageHeader from "../../components/common/PageHeader";
import EmptyState from "../../components/common/EmptyState";

const sectionsData = [
  {
    id: 1,
    name: "شعبة التربية العملية 1",
    students: [
      { id: 101, name: "أحمد محمد" },
      { id: 102, name: "سارة خالد" },
      { id: 103, name: "محمد يوسف" },
    ],
  },
  {
    id: 2,
    name: "شعبة التربية العملية 2",
    students: [
      { id: 201, name: "آية ناصر" },
      { id: 202, name: "ليان سمير" },
      { id: 203, name: "يوسف زيدان" },
    ],
  },
];

const initialForm = {
  sectionId: "",
  targetMode: "all",
  selectedStudentIds: [],
  title: "",
  description: "",
  taskType: "واجب",
  dueDate: "",
  attachment: "",
  status: "نشطة",
};

export default function Tasks() {
  const [tasks, setTasks] = useState([
    {
      id: 1,
      sectionId: 1,
      sectionName: "شعبة التربية العملية 1",
      targetMode: "all",
      selectedStudentIds: [],
      targetLabel: "جميع طلبة الشعبة",
      title: "إعداد خطة درس",
      description: "إعداد خطة درس يومية ورفعها قبل موعد الحصة.",
      taskType: "خطة درس",
      dueDate: "2026-04-10",
      attachment: "",
      status: "نشطة",
    },
    {
      id: 2,
      sectionId: 2,
      sectionName: "شعبة التربية العملية 2",
      targetMode: "specific",
      selectedStudentIds: [201, 202],
      targetLabel: "آية ناصر، ليان سمير",
      title: "تقرير زيارة ميدانية",
      description: "كتابة تقرير موجز عن الزيارة الميدانية الأخيرة.",
      taskType: "تقرير",
      dueDate: "2026-04-14",
      attachment: "",
      status: "قيد المتابعة",
    },
  ]);

  const [form, setForm] = useState(initialForm);
  const [isFormOpen, setIsFormOpen] = useState(false);
  const [editingTaskId, setEditingTaskId] = useState(null);

  const selectedSection = useMemo(
    () => sectionsData.find((section) => String(section.id) === String(form.sectionId)),
    [form.sectionId]
  );

  const availableStudents = selectedSection?.students || [];

  const openAddForm = () => {
    setForm(initialForm);
    setEditingTaskId(null);
    setIsFormOpen(true);
  };

  const openEditForm = (task) => {
    setForm({
      sectionId: task.sectionId,
      targetMode: task.targetMode,
      selectedStudentIds: task.selectedStudentIds || [],
      title: task.title,
      description: task.description,
      taskType: task.taskType,
      dueDate: task.dueDate,
      attachment: task.attachment || "",
      status: task.status,
    });
    setEditingTaskId(task.id);
    setIsFormOpen(true);
  };

  const closeForm = () => {
    setForm(initialForm);
    setEditingTaskId(null);
    setIsFormOpen(false);
  };

  const handleChange = (e) => {
    const { name, value } = e.target;

    setForm((prev) => {
      if (name === "sectionId") {
        return {
          ...prev,
          sectionId: value,
          selectedStudentIds: [],
        };
      }

      if (name === "targetMode") {
        return {
          ...prev,
          targetMode: value,
          selectedStudentIds: value === "all" ? [] : prev.selectedStudentIds,
        };
      }

      return {
        ...prev,
        [name]: value,
      };
    });
  };

  const handleStudentToggle = (studentId) => {
    setForm((prev) => {
      const exists = prev.selectedStudentIds.includes(studentId);

      return {
        ...prev,
        selectedStudentIds: exists
          ? prev.selectedStudentIds.filter((id) => id !== studentId)
          : [...prev.selectedStudentIds, studentId],
      };
    });
  };

  const getTargetLabel = () => {
    if (!selectedSection) return "";

    if (form.targetMode === "all") {
      return "جميع طلبة الشعبة";
    }

    const names = availableStudents
      .filter((student) => form.selectedStudentIds.includes(student.id))
      .map((student) => student.name);

    return names.join("، ");
  };

  const handleSubmit = (e) => {
    e.preventDefault();

    if (!form.sectionId || !form.title.trim() || !form.description.trim() || !form.dueDate) {
      alert("يرجى تعبئة الشعبة، عنوان المهمة، الوصف، وموعد التسليم");
      return;
    }

    if (form.targetMode === "specific" && !form.selectedStudentIds.length) {
      alert("يرجى اختيار طالب واحد على الأقل");
      return;
    }

    const section = sectionsData.find(
      (item) => String(item.id) === String(form.sectionId)
    );

    const payload = {
      ...form,
      sectionId: Number(form.sectionId),
      sectionName: section?.name || "",
      targetLabel: getTargetLabel(),
    };

    if (editingTaskId) {
      setTasks((prev) =>
        prev.map((task) =>
          task.id === editingTaskId ? { ...task, ...payload } : task
        )
      );
      alert("تم تعديل المهمة بنجاح");
    } else {
      const newTask = {
        id: Date.now(),
        ...payload,
      };

      setTasks((prev) => [newTask, ...prev]);
      alert("تمت إضافة المهمة بنجاح");
    }

    closeForm();
  };

  const handleDelete = (taskId) => {
    const confirmed = window.confirm("هل تريد حذف هذه المهمة؟");
    if (!confirmed) return;

    setTasks((prev) => prev.filter((task) => task.id !== taskId));
  };

  const getBadgeClass = (status) => {
    if (status === "مكتملة") return "badge-success";
    if (status === "قيد المتابعة") return "badge-warning";
    return "badge-primary";
  };

  return (
    <>
      <PageHeader
        title="إدارة المهام"
        subtitle="إضافة المهام للطلبة حسب الشعبة ومتابعة تنفيذها"
      />

      <div className="page-actions">
        <button className="btn-primary-custom" onClick={openAddForm}>
          إضافة مهمة جديدة
        </button>
      </div>

      {isFormOpen && (
        <div className="form-card mb-3">
          <form onSubmit={handleSubmit}>
            <div className="page-grid two-cols">
              <div className="form-group-custom">
                <label className="form-label-custom">الشعبة</label>
                <select
                  name="sectionId"
                  className="form-select-custom"
                  value={form.sectionId}
                  onChange={handleChange}
                >
                  <option value="">اختر الشعبة</option>
                  {sectionsData.map((section) => (
                    <option key={section.id} value={section.id}>
                      {section.name}
                    </option>
                  ))}
                </select>
              </div>

              <div className="form-group-custom">
                <label className="form-label-custom">نوع المهمة</label>
                <select
                  name="taskType"
                  className="form-select-custom"
                  value={form.taskType}
                  onChange={handleChange}
                >
                  <option value="واجب">واجب</option>
                  <option value="خطة درس">خطة درس</option>
                  <option value="تقرير">تقرير</option>
                  <option value="تحليل موقف تدريسي">تحليل موقف تدريسي</option>
                  <option value="ملف مرفق">ملف مرفق</option>
                </select>
              </div>
            </div>

            <div className="page-grid two-cols">
              <div className="form-group-custom">
                <label className="form-label-custom">توجيه المهمة إلى</label>
                <select
                  name="targetMode"
                  className="form-select-custom"
                  value={form.targetMode}
                  onChange={handleChange}
                >
                  <option value="all">جميع طلبة الشعبة</option>
                  <option value="specific">طلبة محددين</option>
                </select>
              </div>

              <div className="form-group-custom">
                <label className="form-label-custom">موعد التسليم</label>
                <input
                  type="date"
                  name="dueDate"
                  className="form-control-custom"
                  value={form.dueDate}
                  onChange={handleChange}
                />
              </div>
            </div>

            {form.targetMode === "specific" && selectedSection && (
              <div className="form-group-custom">
                <label className="form-label-custom">اختيار الطلبة</label>
                <div className="surface" style={{ padding: "14px" }}>
                  <div className="list-clean">
                    {availableStudents.map((student) => (
                      <label
                        key={student.id}
                        style={{
                          display: "flex",
                          alignItems: "center",
                          gap: "10px",
                          cursor: "pointer",
                        }}
                      >
                        <input
                          type="checkbox"
                          checked={form.selectedStudentIds.includes(student.id)}
                          onChange={() => handleStudentToggle(student.id)}
                        />
                        <span>{student.name}</span>
                      </label>
                    ))}
                  </div>
                </div>
              </div>
            )}

            <div className="form-group-custom">
              <label className="form-label-custom">عنوان المهمة</label>
              <input
                type="text"
                name="title"
                className="form-control-custom"
                value={form.title}
                onChange={handleChange}
              />
            </div>

            <div className="form-group-custom">
              <label className="form-label-custom">وصف المهمة</label>
              <textarea
                name="description"
                className="form-textarea-custom"
                value={form.description}
                onChange={handleChange}
              />
            </div>

            <div className="page-grid two-cols">
              <div className="form-group-custom">
                <label className="form-label-custom">رابط أو اسم مرفق</label>
                <input
                  type="text"
                  name="attachment"
                  className="form-control-custom"
                  value={form.attachment}
                  onChange={handleChange}
                  placeholder="مثال: lesson-plan.docx"
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
                  <option value="نشطة">نشطة</option>
                  <option value="قيد المتابعة">قيد المتابعة</option>
                  <option value="مكتملة">مكتملة</option>
                </select>
              </div>
            </div>

            <div className="table-actions">
              <button type="submit" className="btn-primary-custom">
                {editingTaskId ? "حفظ التعديلات" : "إضافة المهمة"}
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

      {!tasks.length ? (
        <EmptyState
          title="لا توجد مهام"
          description="لم يتم إنشاء أي مهمة حتى الآن."
        />
      ) : (
        <div className="list-clean">
          {tasks.map((task) => (
            <div key={task.id} className="list-item-card">
              <div className="panel-header">
                <div>
                  <h4 className="panel-title">{task.title}</h4>
                  <p className="panel-subtitle">{task.description}</p>
                </div>

                <span className={`badge-custom ${getBadgeClass(task.status)}`}>
                  {task.status}
                </span>
              </div>

              <div className="list-clean" style={{ marginTop: "12px", gap: "6px" }}>
                <span className="text-soft">الشعبة: {task.sectionName}</span>
                <span className="text-soft">الفئة المستهدفة: {task.targetLabel}</span>
                <span className="text-soft">نوع المهمة: {task.taskType}</span>
                <span className="text-soft">موعد التسليم: {task.dueDate}</span>
                {task.attachment ? (
                  <span className="text-soft">المرفق: {task.attachment}</span>
                ) : null}
              </div>

              <div className="page-actions" style={{ marginTop: "12px" }}>
                <div className="table-actions">
                  <button
                    className="btn-light-custom btn-sm-custom"
                    onClick={() => openEditForm(task)}
                  >
                    تعديل
                  </button>
                  <button
                    className="btn-danger-custom btn-sm-custom"
                    onClick={() => handleDelete(task.id)}
                  >
                    حذف
                  </button>
                </div>
              </div>
            </div>
          ))}
        </div>
      )}
    </>
  );
}