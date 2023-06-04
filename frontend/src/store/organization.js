import { defineStore } from "pinia";

export const useOrganizationStore = defineStore("organization", {
  state: () => ({
    id: null,
    name: "",
    description: "",
  }),
  actions: {
    setOrganization({ id, name, description }) {
      this.id = id;
      this.name = name;
      this.description = description;
    },
  },
});
