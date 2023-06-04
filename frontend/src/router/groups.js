export const groupsRoutes = [
  {
    path: "/dashboard/groups",
    name: "Dashboard-Groups",
    layout: "dashboard",
    component: () => import("../views/dashboard/group/Groups.vue"),
  },
  {
    path: "/dashboard/groups/:id",
    name: "Group Home",
    layout: "dashboard",
    component: () => import("../views/dashboard/group/Home.vue"),
  },
  {
    path: "/dashboard/groups/:id/users",
    name: "Group Users",
    layout: "dashboard",
    component: () => import("../views/dashboard/group/Users.vue"),
  },
  {
    path: "/dashboard/groups/:id/topics",
    name: "Group Topics",
    layout: "dashboard",
    component: () => import("../views/dashboard/group/Topics.vue"),
  },
];
