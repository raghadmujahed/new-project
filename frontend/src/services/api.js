// src/services/api.js
import axios from "axios";

const apiClient = axios.create({
  baseURL: "/api", // ← استخدام الـ proxy
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

// -------------------- AUTH --------------------
export const login = async (credentials) => {
  const response = await apiClient.post("/login", credentials);
  return response.data;
};

export const logout = async () => {
  const response = await apiClient.post("/logout");
  localStorage.removeItem("access_token");
  localStorage.removeItem("user");
  return response.data;
};

export const getCurrentUser = async () => {
  const response = await apiClient.get("/user");
  return response.data;
};

// -------------------- TRAINING REQUESTS --------------------
export const getTrainingRequests = async (params = {}) => {
  const response = await apiClient.get("/training-requests", { params });
  return response.data;
};

export const createTrainingRequest = async (data) => {
  const response = await apiClient.post("/training-requests", data);
  return response.data;
};

export const sendToDirectorate = async (id, letterData) => {
  return apiClient.post(`/training-requests/${id}/send-to-directorate`, letterData);
};

export const directorateApprove = async (id, data) => {
  return apiClient.post(`/training-requests/${id}/directorate-approve`, data);
};

export const sendToSchool = async (id, letterData) => {
  return apiClient.post(`/training-requests/${id}/send-to-school`, letterData);
};

export const schoolApprove = async (id, data) => {
  return apiClient.post(`/training-requests/${id}/school-approve`, data);
};

// -------------------- DASHBOARD --------------------
export const getDashboardStats = () => apiClient.get('/dashboard/stats').then(res => res.data);

// -------------------- USERS --------------------
export const getUsers = (params) => apiClient.get('/users', { params }).then(res => res.data);
export const getUser = async (id) => {
  const response = await apiClient.get(`/users/${id}`);
  return response.data;
};
export const createUser = (data) => apiClient.post('/users', data).then(res => res.data);
export const updateUser = (id, data) => apiClient.put(`/users/${id}`, data).then(res => res.data);
export const deleteUser = (id) => apiClient.delete(`/users/${id}`).then(res => res.data);
export const changeUserStatus = (id, status) => apiClient.patch(`/users/${id}/status`, { status }).then(res => res.data);

// -------------------- ROLES & PERMISSIONS --------------------
export const getRoles = () => apiClient.get('/roles').then(res => res.data);
export const getRole = async (id) => {
  const response = await apiClient.get(`/roles/${id}`);
  return response.data;
};
export const createRole = (data) => apiClient.post('/roles', data).then(res => res.data);
export const updateRole = (id, data) => apiClient.put(`/roles/${id}`, data).then(res => res.data);
export const deleteRole = (id) => apiClient.delete(`/roles/${id}`).then(res => res.data);
export const getPermissions = () => apiClient.get('/permissions').then(res => res.data);
export const getPermission = async (id) => {
  const response = await apiClient.get(`/permissions/${id}`);
  return response.data;
};
export const updateRolePermissions = async (roleId, permissionIds) => {
  const response = await apiClient.put(`/roles/${roleId}/permissions`, { permissions: permissionIds });
  return response.data;
};

// -------------------- DEPARTMENTS --------------------
export const getDepartments = () => apiClient.get('/departments').then(res => res.data);
export const getDepartment = async (id) => {
  const response = await apiClient.get(`/departments/${id}`);
  return response.data;
};
export const createDepartment = (data) => apiClient.post('/departments', data).then(res => res.data);
export const updateDepartment = (id, data) => apiClient.put(`/departments/${id}`, data).then(res => res.data);
export const deleteDepartment = (id) => apiClient.delete(`/departments/${id}`).then(res => res.data);

// -------------------- COURSES --------------------
export const getCourses = (params) => apiClient.get('/courses', { params }).then(res => res.data);
export const getCourse = async (id) => {
  const response = await apiClient.get(`/courses/${id}`);
  return response.data;
};
export const createCourse = (data) => apiClient.post('/courses', data).then(res => res.data);
export const updateCourse = (id, data) => apiClient.put(`/courses/${id}`, data).then(res => res.data);
export const deleteCourse = (id) => apiClient.delete(`/courses/${id}`).then(res => res.data);

// -------------------- SECTIONS --------------------
export const getSections = (params) => apiClient.get('/sections', { params }).then(res => res.data);
export const getSection = async (id) => {
  const response = await apiClient.get(`/sections/${id}`);
  return response.data;
};
export const createSection = (data) => apiClient.post('/sections', data).then(res => res.data);
export const updateSection = (id, data) => apiClient.put(`/sections/${id}`, data).then(res => res.data);
export const deleteSection = (id) => apiClient.delete(`/sections/${id}`).then(res => res.data);
export const bulkUploadSections = (data) => apiClient.post('/sections/bulk-upload', data).then(res => res.data);

// -------------------- ENROLLMENTS --------------------
export const getEnrollments = (params) => apiClient.get('/enrollments', { params }).then(res => res.data);
export const getEnrollment = async (id) => {
  const response = await apiClient.get(`/enrollments/${id}`);
  return response.data;
};
export const createEnrollment = (data) => apiClient.post('/enrollments', data).then(res => res.data);
export const updateEnrollment = (id, data) => apiClient.put(`/enrollments/${id}`, data).then(res => res.data);
export const deleteEnrollment = (id) => apiClient.delete(`/enrollments/${id}`).then(res => res.data);
export const enrollStudentInSection = (sectionId, userId) => 
  apiClient.post(`/sections/${sectionId}/enroll`, { user_id: userId }).then(res => res.data);

// -------------------- TRAINING SITES --------------------
export const getTrainingSites = (params) => apiClient.get('/training-sites', { params }).then(res => res.data);
export const getTrainingSite = async (id) => {
  const response = await apiClient.get(`/training-sites/${id}`);
  return response.data;
};
export const createTrainingSite = (data) => apiClient.post('/training-sites', data).then(res => res.data);
export const updateTrainingSite = (id, data) => apiClient.put(`/training-sites/${id}`, data).then(res => res.data);
export const deleteTrainingSite = (id) => apiClient.delete(`/training-sites/${id}`).then(res => res.data);

// -------------------- TRAINING PERIODS --------------------
export const getTrainingPeriods = () => apiClient.get('/training-periods').then(res => res.data);
export const getTrainingPeriod = async (id) => {
  const response = await apiClient.get(`/training-periods/${id}`);
  return response.data;
};
export const createTrainingPeriod = (data) => apiClient.post('/training-periods', data).then(res => res.data);
export const updateTrainingPeriod = (id, data) => apiClient.put(`/training-periods/${id}`, data).then(res => res.data);
export const deleteTrainingPeriod = (id) => apiClient.delete(`/training-periods/${id}`).then(res => res.data);
export const setActivePeriod = (id) => apiClient.patch(`/training-periods/${id}/set-active`).then(res => res.data);
export const getActiveTrainingPeriod = () => apiClient.get('/training-periods/active').then(res => res.data);

// -------------------- ANNOUNCEMENTS --------------------
export const getAnnouncements = () => apiClient.get('/announcements').then(res => res.data);
export const getAnnouncement = async (id) => {
  const response = await apiClient.get(`/announcements/${id}`);
  return response.data;
};
export const createAnnouncement = (data) => apiClient.post('/announcements', data).then(res => res.data);
export const updateAnnouncement = (id, data) => apiClient.put(`/announcements/${id}`, data).then(res => res.data);
export const deleteAnnouncement = (id) => apiClient.delete(`/announcements/${id}`).then(res => res.data);
export const getLatestAnnouncement = async () => {
  const response = await apiClient.get('/announcements', { params: { per_page: 1 } });
  return response.data?.data?.[0] || null;
};

// -------------------- BACKUPS --------------------
export const getBackups = () => apiClient.get('/backups').then(res => res.data);
export const createBackup = (data) => apiClient.post('/backups', data).then(res => res.data);
export const restoreBackup = (id) => apiClient.post(`/backups/${id}/restore`).then(res => res.data);
export const deleteBackup = (id) => apiClient.delete(`/backups/${id}`).then(res => res.data);

// -------------------- ACTIVITY LOGS --------------------
export const deleteActivityLog = (id) => apiClient.delete(`/activity-logs/${id}`).then(res => res.data);
export const getRecentActivities = async (limit = 5) => {
  const response = await apiClient.get('/activity-logs', { params: { per_page: limit } });
  return response.data;
};

// -------------------- FEATURE FLAGS --------------------
export const getFeatureFlags = async () => {
  const response = await apiClient.get('/feature-flags', { params: { _: Date.now() } });
  return response.data;
};
export const updateFeatureFlag = (id, isOpen) => apiClient.patch(`/feature-flags/${id}`, { is_open: isOpen }).then(res => res.data);

// -------------------- EVALUATION TEMPLATES --------------------
export const getEvaluationTemplates = () => apiClient.get('/evaluation-templates').then(res => res.data);
export const getEvaluationTemplate = async (id) => {
  const response = await apiClient.get(`/evaluation-templates/${id}`);
  return response.data;
};
export const createEvaluationTemplate = (data) => apiClient.post('/evaluation-templates', data).then(res => res.data);
export const updateEvaluationTemplate = (id, data) => apiClient.put(`/evaluation-templates/${id}`, data).then(res => res.data);
export const deleteEvaluationTemplate = (id) => apiClient.delete(`/evaluation-templates/${id}`).then(res => res.data);
export const addTemplateItem = (templateId, data) => apiClient.post(`/evaluation-templates/${templateId}/items`, data).then(res => res.data);
export const updateTemplateItem = (itemId, data) => apiClient.put(`/evaluation-items/${itemId}`, data).then(res => res.data);
export const deleteTemplateItem = (itemId) => apiClient.delete(`/evaluation-items/${itemId}`).then(res => res.data);

// -------------------- STUDENT SPECIFIC --------------------
export const getStudentTrainingRequests = async () => {
  const response = await apiClient.get('/user');
  return response.data;
};
export const createStudentTrainingRequest = async (data) => {
  const response = await apiClient.post('/student/training-requests', data);
  return response.data;
};
export const getStudentSchedule = async () => {
  const response = await apiClient.get('/student/schedule');
  return response.data;
};
export const getStudentTrainingLogs = async () => {
  const response = await apiClient.get('/student/training-logs');
  return response.data;
};
export const createStudentTrainingLog = async (data) => {
  const response = await apiClient.post('/student/training-logs', data);
  return response.data;
};
export const updateStudentTrainingLog = async (id, data) => {
  const response = await apiClient.put(`/student/training-logs/${id}`, data);
  return response.data;
};
export const submitStudentTrainingLog = async (id) => {
  const response = await apiClient.post(`/student/training-logs/${id}/submit`);
  return response.data;
};
export const getStudentPortfolio = async () => {
  const response = await apiClient.get('/my-portfolio');
  return response.data;
};
export const addPortfolioEntry = async (data) => {
  const response = await apiClient.post('/student/portfolio/entries', data);
  return response.data;
};
export const updatePortfolioEntry = async (id, data) => {
  const response = await apiClient.put(`/student/portfolio/entries/${id}`, data);
  return response.data;
};
export const deletePortfolioEntry = async (id) => {
  const response = await apiClient.delete(`/student/portfolio/entries/${id}`);
  return response.data;
};
export const getStudentTasks = async () => {
  const response = await apiClient.get('/student/tasks');
  return response.data;
};
export const submitStudentTask = async (taskId, data) => {
  const response = await apiClient.post(`/student/tasks/${taskId}/submit`, data);
  return response.data;
};
export const getStudentNotifications = async () => {
  const response = await apiClient.get('/student/notifications');
  return response.data;
};
export const markNotificationAsRead = async (id) => {
  const response = await apiClient.patch(`/student/notifications/${id}/read`);
  return response.data;
};
// -------------------- BACKUPS --------------------
export const getBackupDetails = (id) => apiClient.get(`/backups/${id}`).then(res => res.data);



// الرفع الجماعي للتسجيلات (إضافة طلاب إلى شعب متعددة)
export const bulkEnrollStudents = (data) => apiClient.post('/enrollments/bulk', data).then(res => res.data);
// في src/services/api.js




// التأكد من أن apiClient هو المستخدم
export const getActivityLogs = async (params = {}) => {
  const response = await apiClient.get('/activity-logs', { params });
  return response.data;
};

export const getBackupTableData = (backupId, tableName) =>
  apiClient.get(`/backups/${backupId}/table/${encodeURIComponent(tableName)}`).then(res => res.data);