import Vue from "vue";
import App from "./App.vue";
import router from "./router";
import vuetify from "./plugins/vuetify";
import axios from 'axios'

Vue.config.productionTip = false;

const base = axios.create({
  baseURL: 'http://localhost/api',
});

Vue.prototype.$http = base;
Vue.config.productionTip = false;
Vue.config.silent = true;

new Vue({
  el: '#app',
  router: router,
  vuetify: vuetify,
  render: h => h(App)
}).$mount("#app");
