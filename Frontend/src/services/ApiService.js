import axios from "axios";
import { startLoading, stopLoading } from '@/utils/loading.js';

// PRODUTOS
export const PRODUCTS_ENDPOINT = "produtos";
export const PURCHASES_ENDPOINT = "compras";
export const SALES_ENDPOINT = "vendas";

const ApiService = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || "http://localhost:3000/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Interceptor para mostrar loading antes da requisição
ApiService.interceptors.request.use(
  (config) => {
    startLoading();
    return config;
  },
  (error) => {
    stopLoading();
    return Promise.reject(error);
  }
);

// Interceptor para esconder loading após a resposta
ApiService.interceptors.response.use(
  (response) => {
    stopLoading();
    return response;
  },
  (error) => {
    stopLoading();
    return Promise.reject(error);
  }
);

export default ApiService;
