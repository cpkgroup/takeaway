import Vue from "vue";
import VueRouter from "vue-router";
import Logs from "../views/Logs.vue";
import Compose from "../views/Compose.vue";
import About from "../views/About.vue";

Vue.use(VueRouter);

const routes = [
  {
    path: "/",
    name: "Logs",
    component: Logs
  },
  {
    path: "/compose",
    name: "compose",
    component: Compose
  },
  {
    path: "/about",
    name: "about",
    component: About
  }
];

const router = new VueRouter({
  routes
});

export default router;
