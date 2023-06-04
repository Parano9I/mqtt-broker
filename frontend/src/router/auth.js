export const authRoutes = [
  {
    path: "/auth/sign-in",
    name: "Sign-In",
    component: () => import("../views/auth/Sign-In.vue"),
  },
  {
    path: "/auth/sign-up",
    name: "Sign-Up",
    component: () => import("../views/auth/Sign-Up.vue"),
  },
];
