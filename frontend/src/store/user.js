import { defineStore } from "pinia";

export const useUserStore = defineStore("user", {
  state: () => ({
    id: null,
    login: "",
    email: "",
    role: "",
    token: "",
  }),
  actions: {
    setUser({ id, login, email, role }) {
      this.id = id;
      this.login = login;
      this.email = email;
      this.role = role;
    },
    setToken(token) {
      this.token = token;
    },
  },
});
