<template>
  <!-- Main Sidebar -->
  <component :is="navbarFixed ? 'a-affix' : 'div'" :offset-top="top">
    <!-- Layout Header -->
    <a-layout-header>
      <a-row type="flex">
        <!-- Header Breadcrumbs & Title Column -->
        <a-col :span="24" :md="6">
          <!-- Header Page Title -->
          <div class="ant-page-header-heading">
            <span class="ant-page-header-heading-title">{{
              this.$route.name
            }}</span>
          </div>
          <!-- / Header Page Title -->
        </a-col>
        <!-- / Header Breadcrumbs & Title Column -->
        <!-- Header Control Column -->
        <a-col :span="24" :md="18" class="header-control">
          {{ userState.$state.login }}
        </a-col>
        <!-- / Header Control Column -->
      </a-row>
    </a-layout-header>
    <!--  /Layout Header -->
  </component>
  <!-- / Main Sidebar -->
</template>

<script>
import { useUserStore } from "@/store/user";

export default {
  props: {
    navbarFixed: {
      type: Boolean,
      default: false,
    },
    sidebarCollapsed: {
      type: Boolean,
      default: false,
    },
  },
  data() {
    return {
      userState: useUserStore(),
      top: 0,
      wrapper: document.body,
    };
  },
  methods: {
    resizeEventHandler() {
      this.top = this.top ? 0 : -0.01;
    },
  },
  mounted: function () {
    this.wrapper = document.getElementById("layout-dashboard");
  },
  created() {
    window.addEventListener("resize", this.resizeEventHandler);
  },
  destroyed() {
    window.removeEventListener("resize", this.resizeEventHandler);
  },
};
</script>
