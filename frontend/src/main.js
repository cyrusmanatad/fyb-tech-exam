import { createApp } from "vue";
import "./style.css";
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import "primeicons/primeicons.css";
import router from "./router";

import App from "./App.vue";

const app = createApp(App);

app.use(router);
app.use(Toast);

app.mount("#app");
