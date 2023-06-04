import ApiService from "@/services/http/api/ApiService";
import httpClient from "@/services/http";

export default class OrganizationApiService extends ApiService {
  async get() {
    return this.axiosCall({ method: "get", url: "/organizations" });
  }

  async create(data) {
    return this.axiosCall({
      method: "post",
      url: "/organizations",
      data: data,
    });
  }
}

export const organizationAPI = new OrganizationApiService(httpClient);
