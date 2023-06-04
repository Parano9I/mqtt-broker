import httpClient from "@/services/http";
import ApiService from "@/services/http/api/ApiService";

export default class UsersApiService extends ApiService {
  async create(data) {
    return await this.axiosCall({ method: "post", url: "/users", data: data });
  }

  async getAll() {
    return await this.axiosCall({ method: "get", url: "/users" });
  }
}

export const usersApi = new UsersApiService(httpClient);
