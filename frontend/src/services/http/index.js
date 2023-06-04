import axios from "axios";
import { useUserStore } from "@/store/user";
import router from "@/router";
import { app } from "@/main";

const httpClient = axios.create({
  baseURL: `http://localhost/api`,
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
  },
});

httpClient.interceptors.request.use(
  (config) => {
    const token = useUserStore().$state.token;
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
      return config;
    }
    return config;
  },
  (err) => {
    return Promise.reject(err);
  }
);

httpClient.interceptors.response.use(
  (response) => {
    return response;
  },
  async (error) => {
    if (error.response) {
      const currentRoute = await router.currentRoute.value;

      switch (error.response.status) {
        case 401:
          useUserStore().setToken("");
          await router.push("/auth/sign-in");
          if (currentRoute.path !== "/auth/sign-in") {
            await router.push("/auth/sign-in");
          }
      }
    }
    return Promise.reject(error.response);
  }
);

export default httpClient;
