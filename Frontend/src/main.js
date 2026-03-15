import { createApp } from "vue";
import App from "./App.vue";
import router from "./router";

import PrimeVue from "primevue/config";
import Tooltip from "primevue/tooltip";
import ToastService from "primevue/toastservice";
import Aura from "@primeuix/themes/aura";
import { updateSurfacePalette } from "@primeuix/themes";
import { loadPressets } from "@/assets/js/all.js";

import "@/assets/css/index.css";
import "primeicons/primeicons.css";
import "primeflex/primeflex.css";

import { isLoading } from '@/utils/loading.js';

// Use PrimeVue surface palette to ensure background matches slate-100 in light mode
updateSurfacePalette({ light: '#f1f5f9' });

const app = createApp(App);

loadPressets();

app.use(router);
app.use(ToastService);
app.use(PrimeVue, {
  theme: {
    preset: Aura,
    options: {
      darkModeSelector: ".dark",
    },
  },
});

app.directive("tooltip", Tooltip);

// Provide global loading state
app.provide('isLoading', isLoading);

app.mount("#app");
