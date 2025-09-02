import axios from "axios";

const api = axios.create({
  baseURL: "http://localhost:8000/api", // عدل حسب إعدادات السيرفر
  headers: {
    Accept: "application/json"
  }
});

export default api;
