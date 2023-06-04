import ApiService from "@/services/http/api/ApiService";
import httpClient from "@/services/http";

export default class SensorsApiService extends ApiService {
  async create(params, data) {
    return await this.axiosCall({
      method: "post",
      url: `/groups/${params.group}/topics/${params.topic}/sensors`,
      data: data,
    });
  }

  async delete(params) {
    return await this.axiosCall({
      method: "delete",
      url: `/groups/${params.group}/topics/${params.topic}/sensors/${params.sensor}`,
    });
  }

  async getMetrics(sensorId, params) {
    return await this.axiosCall({
      method: "get",
      url: `/sensors/${sensorId}/metrics`,
      params: params,
    });
  }

  async getAll() {
    return await this.axiosCall({ method: "get", url: `/sensors` });
  }
}

export const sensorsAPI = new SensorsApiService(httpClient);
