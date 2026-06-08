import { api } from '../api/api';

export const login = async (email: string, password: string) => {
  try {
    const response = await api.post('/login', { email, password });
    return response.data; // Retorna o JWT e dados do usuário
  } catch (error: any) {
    console.error("Erro no login:", error.response?.data || error.message);
    throw error;
  }
};