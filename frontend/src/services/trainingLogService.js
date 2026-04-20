import api from "./api";

export const getTrainingLogs = async (params = {}) => {
  const response = await api.get("/training-logs", { params });
  return response.data;
};

export const getTrainingLogById = async (logId) => {
  const response = await api.get(`/training-logs/${logId}`);
  return response.data;
};

export const createTrainingLog = async (payload) => {
  const response = await api.post("/training-logs", payload);
  return response.data;
};

export const updateTrainingLog = async (logId, payload) => {
  const response = await api.put(`/training-logs/${logId}`, payload);
  return response.data;
};

export const deleteTrainingLog = async (logId) => {
  const response = await api.delete(`/training-logs/${logId}`);
  return response.data;
};

export const submitTrainingLog = async (logId) => {
  const response = await api.post(`/training-logs/${logId}/submit`);
  return response.data;
};