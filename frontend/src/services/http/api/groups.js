import ApiService from "@/services/http/api/ApiService";
import httpClient from "@/services/http";

export default class GroupsApiService extends ApiService {
  async getAll() {
    return await this.axiosCall({ method: "get", url: "/groups" });
  }

  async create(data) {
    return await this.axiosCall({
      method: "post",
      url: "/groups",
      data: data,
    });
  }

  async show(id) {
    return await this.axiosCall({ method: "get", url: `/groups/${id}` });
  }

  async delete(id) {
    return await this.axiosCall({ method: "delete", url: `/groups/${id}` });
  }

  async getAllSensors(groupId) {
    return await this.axiosCall({
      method: "get",
      url: `/groups/${groupId}/sensors`,
    });
  }

  async getAllUsers(groupId) {
    return await this.axiosCall({
      method: "get",
      url: `/groups/${groupId}/users`,
    });
  }

  async addUser(groupId, userId) {
    return await this.axiosCall({
      method: "post",
      url: `/groups/${groupId}/users`,
      data: {
        user_id: userId,
      },
    });
  }

  async removeUser(groupId, userId) {
    return await this.axiosCall({
      method: "delete",
      url: `/groups/${groupId}/users/${userId}`,
    });
  }
}
export const groupsAPI = new GroupsApiService(httpClient);
