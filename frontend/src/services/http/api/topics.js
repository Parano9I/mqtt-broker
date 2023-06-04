import ApiService from "@/services/http/api/ApiService";
import httpClient from "@/services/http";

export default class TopicsApiService extends ApiService {
  async getAllByGroup(groupId) {
    return await this.axiosCall({
      method: "get",
      url: `/groups/${groupId}/topics`,
    });
  }

  async create(params, data) {
    return await this.axiosCall({
      method: "post",
      url: `/groups/${params.group}/topics`,
      data: data,
    });
  }

  async delete(params) {
    return await this.axiosCall({
      method: "delete",
      url: `/groups/${params.group}/topics/${params.topic}`,
    });
  }
}

export const topicsAPI = new TopicsApiService(httpClient);
