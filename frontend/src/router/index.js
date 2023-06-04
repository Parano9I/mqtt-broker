import Vue from "vue";
import VueRouter from "vue-router";
import { authRoutes } from "./auth";
import { organizationRoutes } from "@/router/organization";
import { usersRoutes } from "@/router/users";
import { groupsRoutes } from "@/router/groups";

Vue.use(VueRouter);

let routes = [
  {
    // will match everything
    path: "*",
    component: () => import("../views/404.vue"),
  },
  {
    path: "/",
    name: "Home",
    redirect: "/dashboard",
  },
  ...authRoutes,
  ...organizationRoutes,
  ...usersRoutes,
  ...groupsRoutes,
  {
    path: "/dashboard",
    name: "Dashboard",
    layout: "dashboard",
    // route level code-splitting
    // this generates a separate chunk (about.[hash].js) for this route
    // which is lazy-loaded when the route is visited.
    component: () =>
      import(/* webpackChunkName: "dashboard" */ "../views/Dashboard.vue"),
  },
  {
    path: "/layout",
    name: "Layout",
    layout: "dashboard",
    component: () => import("../views/Layout.vue"),
  },
  {
    path: "/tables",
    name: "Tables",
    layout: "dashboard",
    component: () => import("../views/Tables.vue"),
  },
  {
    path: "/billing",
    name: "Billing",
    layout: "dashboard",
    component: () => import("../views/Billing.vue"),
  },
  {
    path: "/rtl",
    name: "RTL",
    layout: "dashboard-rtl",
    meta: {
      layoutClass: "dashboard-rtl",
    },
    component: () => import("../views/RTL.vue"),
  },
  {
    path: "/Profile",
    name: "Profile",
    layout: "dashboard",
    meta: {
      layoutClass: "layout-profile",
    },
    component: () => import("../views/Profile.vue"),
  },
];

// Adding layout property from each route to the meta
// object so it can be accessed later.
function addLayoutToRoute(route, parentLayout = "default") {
  route.meta = route.meta || {};
  route.meta.layout = route.layout || parentLayout;

  if (route.children) {
    route.children = route.children.map((childRoute) =>
      addLayoutToRoute(childRoute, route.meta.layout)
    );
  }
  return route;
}

routes = routes.map((route) => addLayoutToRoute(route));

const router = new VueRouter({
  mode: "history",
  base: process.env.BASE_URL,
  routes,
});

export default router;
