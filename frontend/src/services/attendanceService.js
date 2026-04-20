import api from "./api";

export const getAttendance = async (params = {}) => {
  const response = await api.get("/attendance", { params });
  return response.data;
};

export const getAttendanceById = async (attendanceId) => {
  const response = await api.get(`/attendance/${attendanceId}`);
  return response.data;
};

export const markAttendance = async (payload) => {
  const response = await api.post("/attendance", payload);
  return response.data;
};

export const updateAttendance = async (attendanceId, payload) => {
  const response = await api.put(`/attendance/${attendanceId}`, payload);
  return response.data;
};

export const deleteAttendance = async (attendanceId) => {
  const response = await api.delete(`/attendance/${attendanceId}`);
  return response.data;
};