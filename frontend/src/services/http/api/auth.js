import httpClient from "@/services/http";
import ApiService from "@/services/http/api/ApiService";

export default class AuthApiService extends ApiService {
  async login(data) {
    return await this.axiosCall({
      method: "post",
      url: "/auth/login",
      data: data,
    });
  }

  async logout() {
    return await this.axiosCall({ method: "get", url: "/auth/logout" });
  }
}

export const authAPI = new AuthApiService(httpClient);
