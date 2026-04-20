// src/services/api.js
import axios from "axios";

/* ===================== BASE CLIENT ===================== */
const apiClient = axios.create({
  baseURL: "/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
  withCredentials: false,
});

// Inject token automatically
apiClient.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("access_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => Promise.reject(error)
);

/* ===================== HELPERS (ترجع البيانات فقط) ===================== */
const get = (url, config) => apiClient.get(url, config).then((res) => res.data);
const post = (url, data) => apiClient.post(url, data).then((res) => res.data);
const put = (url, data) => apiClient.put(url, data).then((res) => res.data);
const patch = (url, data) => apiClient.patch(url, data).then((res) => res.data);
const del = (url) => apiClient.delete(url).then((res) => res.data);

/* ===================== AUTH ===================== */
export const login = (credentials) => post("/login", credentials);

export const logout = async () => {
  const res = await post("/logout");
  localStorage.removeItem("access_token");
  localStorage.removeItem("user");
  return res;
};

export const getCurrentUser = () => get("/user");

/* ===================== DASHBOARD ===================== */
export const getDashboardStats = () => get("/dashboard/stats");
export const getRecentActivities = (limit = 5) =>
  get("/activity-logs", { params: { per_page: limit } });

/* ===================== USERS ===================== */
export const getUsers = (params) => get("/users", { params });
export const getUser = (id) => get(`/users/${id}`);
export const createUser = (data) => post("/users", data);
export const updateUser = (id, data) => put(`/users/${id}`, data);
export const deleteUser = (id) => del(`/users/${id}`);
export const changeUserStatus = (id, status) =>
  patch(`/users/${id}/status`, { status });

/* ===================== ROLES & PERMISSIONS ===================== */
export const getRoles = () => get("/roles");
export const getRole = (id) => get(`/roles/${id}`);
export const createRole = (data) => post("/roles", data);
export const updateRole = (id, data) => put(`/roles/${id}`, data);
export const deleteRole = (id) => del(`/roles/${id}`);

export const getPermissions = () => get("/permissions");
export const getPermission = (id) => get(`/permissions/${id}`);
export const updateRolePermissions = (roleId, permissions) =>
  put(`/roles/${roleId}/permissions`, { permissions });

/* ===================== DEPARTMENTS ===================== */
export const getDepartments = (params) => get("/departments", { params });
export const getDepartment = (id) => get(`/departments/${id}`);
export const createDepartment = (data) => post("/departments", data);
export const updateDepartment = (id, data) => put(`/departments/${id}`, data);
export const deleteDepartment = (id) => del(`/departments/${id}`);

/* ===================== COURSES ===================== */
export const getCourses = (params) => get("/courses", { params });
export const getCourse = (id) => get(`/courses/${id}`);
export const createCourse = (data) => post("/courses", data);
export const updateCourse = (id, data) => put(`/courses/${id}`, data);
export const deleteCourse = (id) => del(`/courses/${id}`);

/* ===================== SECTIONS ===================== */
export const getSections = (params) => get("/sections", { params });
export const getSection = (id) => get(`/sections/${id}`);
export const createSection = (data) => post("/sections", data);
export const updateSection = (id, data) => put(`/sections/${id}`, data);
export const deleteSection = (id) => del(`/sections/${id}`);
export const bulkUploadSections = (data) => post("/sections/bulk-upload", data);

/* ===================== ENROLLMENTS ===================== */
export const getEnrollments = (params) => get("/enrollments", { params });
export const getEnrollment = (id) => get(`/enrollments/${id}`);
export const createEnrollment = (data) => post("/enrollments", data);
export const updateEnrollment = (id, data) => put(`/enrollments/${id}`, data);
export const deleteEnrollment = (id) => del(`/enrollments/${id}`);
export const enrollStudentInSection = (sectionId, userId) =>
  post(`/sections/${sectionId}/enroll`, { user_id: userId });
export const bulkEnrollStudents = (data) => post("/enrollments/bulk", data);

/* ===================== TRAINING REQUESTS ===================== */
export const getTrainingRequests = (params) => get("/training-requests", { params });
export const createTrainingRequest = (data) => post("/training-requests", data);
export const sendToDirectorate = (id, data) => post(`/training-requests/${id}/send-to-directorate`, data);
export const directorateApprove = (id, data) => post(`/training-requests/${id}/directorate-approve`, data);
export const sendToSchool = (id, data) => post(`/training-requests/${id}/send-to-school`, data);
export const schoolApprove = (id, data) => post(`/training-requests/${id}/school-approve`, data);

/* ===================== TRAINING SITES ===================== */
export const getTrainingSites = (params) => get("/training-sites", { params });
export const getTrainingSite = (id) => get(`/training-sites/${id}`);
export const createTrainingSite = (data) => post("/training-sites", data);
export const updateTrainingSite = (id, data) => put(`/training-sites/${id}`, data);
export const deleteTrainingSite = (id) => del(`/training-sites/${id}`);

/* ===================== TRAINING PERIODS ===================== */
export const getTrainingPeriods = () => get("/training-periods");
export const getTrainingPeriod = (id) => get(`/training-periods/${id}`);
export const createTrainingPeriod = (data) => post("/training-periods", data);
export const updateTrainingPeriod = (id, data) => put(`/training-periods/${id}`, data);
export const deleteTrainingPeriod = (id) => del(`/training-periods/${id}`);
export const setActivePeriod = (id) => patch(`/training-periods/${id}/set-active`);
export const getActiveTrainingPeriod = () => get("/training-periods/active");

/* ===================== ANNOUNCEMENTS ===================== */
export const getAnnouncements = (params) => get("/announcements", { params });
export const getAnnouncement = (id) => get(`/announcements/${id}`);
export const createAnnouncement = (data) => post("/announcements", data);
export const updateAnnouncement = (id, data) => put(`/announcements/${id}`, data);
export const deleteAnnouncement = (id) => del(`/announcements/${id}`);
export const getLatestAnnouncement = () =>
  get("/announcements", { params: { per_page: 1 } }).then((res) => res.data?.[0] || null);

/* ===================== BACKUPS ===================== */
export const getBackups = () => get("/backups");
export const createBackup = (data) => post("/backups", data);
export const restoreBackup = (id) => post(`/backups/${id}/restore`);
export const deleteBackup = (id) => del(`/backups/${id}`);
export const getBackupDetails = (id) => get(`/backups/${id}`);
export const getBackupTableData = (backupId, tableName) =>
  get(`/backups/${backupId}/table/${encodeURIComponent(tableName)}`);

/* ===================== ACTIVITY LOGS ===================== */
export const getActivityLogs = (params) => get("/activity-logs", { params });
export const deleteActivityLog = (id) => del(`/activity-logs/${id}`);

/* ===================== FEATURE FLAGS ===================== */
export const getFeatureFlags = () => get("/feature-flags", { params: { _: Date.now() } });
export const updateFeatureFlag = (id, is_open) => patch(`/feature-flags/${id}`, { is_open });

/* ===================== EVALUATIONS ===================== */
export const getEvaluationTemplates = () => get("/evaluation-templates");
export const getEvaluationTemplate = (id) => get(`/evaluation-templates/${id}`);
export const createEvaluationTemplate = (data) => post("/evaluation-templates", data);
export const updateEvaluationTemplate = (id, data) => put(`/evaluation-templates/${id}`, data);
export const deleteEvaluationTemplate = (id) => del(`/evaluation-templates/${id}`);
export const addTemplateItem = (id, data) => post(`/evaluation-templates/${id}/items`, data);
export const updateTemplateItem = (id, data) => put(`/evaluation-items/${id}`, data);
export const deleteTemplateItem = (id) => del(`/evaluation-items/${id}`);

/* ===================== STUDENT ===================== */
export const getStudentSchedule = () => get("/student/schedule");
export const getStudentTrainingLogs = () => get("/student/training-logs");
export const createStudentTrainingLog = (data) => post("/student/training-logs", data);
export const updateStudentTrainingLog = (id, data) => put(`/student/training-logs/${id}`, data);
export const submitStudentTrainingLog = (id) => post(`/student/training-logs/${id}/submit`);
export const getStudentPortfolio = () => get("/my-portfolio");
export const addPortfolioEntry = (data) => post("/student/portfolio/entries", data);
export const updatePortfolioEntry = (id, data) => put(`/student/portfolio/entries/${id}`, data);
export const deletePortfolioEntry = (id) => del(`/student/portfolio/entries/${id}`);
export const getStudentTasks = () => get("/student/tasks");
export const submitStudentTask = (taskId, data) => post(`/student/tasks/${taskId}/submit`, data);
export const getStudentNotifications = () => get("/student/notifications");
export const markNotificationAsRead = (id) => patch(`/student/notifications/${id}/read`);
export const getStudentTrainingRequests = () => get("/user/training-requests");