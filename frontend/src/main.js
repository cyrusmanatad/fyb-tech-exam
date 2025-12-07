import { createApp } from "vue";
import { createPinia } from "pinia";
import "./style.css";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import "primeicons/primeicons.css";
import router from "./router";

import App from "./App.vue";

const pinia = createPinia();
const app = createApp(App);

app.use(pinia);
app.use(router);
app.use(Toast);

app.mount("#app");
