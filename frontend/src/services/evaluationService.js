import api from "./api";

export const getEvaluations = async (params = {}) => {
  const response = await api.get("/evaluations", { params });
  return response.data;
};

export const getEvaluationById = async (evaluationId) => {
  const response = await api.get(`/evaluations/${evaluationId}`);
  return response.data;
};

export const createEvaluation = async (payload) => {
  const response = await api.post("/evaluations", payload);
  return response.data;
};

export const updateEvaluation = async (evaluationId, payload) => {
  const response = await api.put(`/evaluations/${evaluationId}`, payload);
  return response.data;
};