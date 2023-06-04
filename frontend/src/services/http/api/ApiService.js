export default class ApiService {
  axiosInstance;
  constructor(axiosInstance) {
    this.axiosInstance = axiosInstance;
  }

  async axiosCall(config) {
    try {
      const { data } = await this.axiosInstance.request(config);
      return [null, data];
    } catch (error) {
      return [error];
    }
  }
}
