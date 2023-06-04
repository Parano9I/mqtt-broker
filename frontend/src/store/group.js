import { defineStore } from "pinia";

export const useGroupStore = defineStore("group", {
  state: () => ({
    id: null,
    title: "",
    description: "",
  }),
  actions: {
    setGroup({ id, title, description }) {
      this.id = id;
      this.title = title;
      this.description = description;
    },
  },
});
