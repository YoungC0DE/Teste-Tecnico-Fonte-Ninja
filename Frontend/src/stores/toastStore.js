import { ref } from "vue";

const history = ref([]);

export function useToastStore() {
  const add = (toast) => {
    history.value.unshift({
      ...toast,
      date: new Date()
    });

    if (history.value.length > 20) {
      history.value.pop();
    }
  };

  const clear = () => {
    history.value = [];
  };

  return {
    history,
    add,
    clear
  };
}