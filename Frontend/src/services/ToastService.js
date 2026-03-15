import { useToast } from "primevue/usetoast";
import { useToastStore } from "@/stores/toastStore";

const ToastService = () => {
  const toast = useToast();
  const toastStore = useToastStore();

  const show = (severity, summary, detail, life = 3000) => {
    const payload = { severity, summary, detail, life };

    toast.add(payload);

    toastStore.add(payload);
  };

  const success = (message, summary = "Sucesso") => {
    show("success", summary, message);
  };

  const error = (message, summary = "Erro") => {
    show("error", summary, message);
  };

  const warn = (message, summary = "Aviso") => {
    show("warn", summary, message);
  };

  const info = (message, summary = "Info") => {
    show("info", summary, message);
  };

  return { success, error, warn, info };
};

export default ToastService;
