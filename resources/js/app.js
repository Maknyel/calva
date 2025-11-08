import '../css/app.css';
import Vue from 'vue';
import router from './router';
import App from './App.vue';
import Toast from "vue-toastification";
import 'vue-toastification/dist/index.css';

Vue.use(Toast, {
  position: "top-right",
  timeout: 3000,
});

new Vue({
  router,
  render: (h) => h(App),
}).$mount('#app');
