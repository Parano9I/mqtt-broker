<!-- 
	This is the main page of the application, the layout component is used here,
	and the router-view is passed to it.
	Layout component is dynamically declared based on the layout for each route,
	specified in routes list router/index.js .
 -->

<template>
  <div id="app">
    <component :is="layout">
      <router-view />
    </component>
  </div>
</template>

<script>
import { organizationAPI } from "@/services/http/api/organization";
import { useOrganizationStore } from "@/store/organization";

export default {
  data() {
    return {
      organizationStore: useOrganizationStore(),
    };
  },
  mounted() {
    this.getOrganization();
  },
  methods: {
    async getOrganization() {
      const [error, data] = await organizationAPI.get();

      if (error) {
        await this.$router.push("/organization/create");
      } else {
        this.organizationStore.setOrganization({
          id: data.data.id,
          ...data.data.attributes,
        });
      }
    },
  },
  computed: {
    layout() {
      return "layout-" + (this.$route.meta.layout || "default").toLowerCase();
    },
  },
};
</script>

<style lang="scss"></style>
