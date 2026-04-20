import api from "./api";

export const getVisits = async (params = {}) => {
  const response = await api.get("/visits", { params });
  return response.data;
};

export const getVisitById = async (visitId) => {
  const response = await api.get(`/visits/${visitId}`);
  return response.data;
};

export const scheduleVisit = async (payload) => {
  const response = await api.post("/visits", payload);
  return response.data;
};

export const updateVisit = async (visitId, payload) => {
  const response = await api.put(`/visits/${visitId}`, payload);
  return response.data;
};

export const deleteVisit = async (visitId) => {
  const response = await api.delete(`/visits/${visitId}`);
  return response.data;
};

export const submitVisitReport = async (visitId, payload) => {
  const response = await api.post(`/visits/${visitId}/report`, payload);
  return response.data;
};