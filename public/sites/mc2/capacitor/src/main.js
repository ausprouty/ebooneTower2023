import App from "./App.vue";
import router from "./router";
import { createApp } from "vue";

import "./assets/styles/appGLOBAL.css";
import "./assets/styles/cardGLOBAL.css";
import "./assets/styles/mc2GLOBAL.css";

const app = createApp(App).use(router);
router.isReady().then(() => {
    app.mount("#app");
});
