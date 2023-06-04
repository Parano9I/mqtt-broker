export const usersRoutes = [
  {
    path: "/dashboard/users",
    name: "Dashboard-Users",
    layout: "dashboard",
    component: () => import("../views/dashboard/user/Users.vue"),
  },
];
