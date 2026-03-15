import { ref } from 'vue';

export const isLoading = ref(false);
let activeRequests = 0;

export const startLoading = () => {
  activeRequests++;
  isLoading.value = true;
};

export const stopLoading = () => {
  activeRequests--;
  if (activeRequests <= 0) {
    activeRequests = 0;
    isLoading.value = false;
  }
};