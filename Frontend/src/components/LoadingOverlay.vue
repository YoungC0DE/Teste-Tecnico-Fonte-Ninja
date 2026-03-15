<template>
  <div v-if="isLoading" class="loading-overlay">
    <ProgressSpinner />
  </div>
</template>

<script setup>
import ProgressSpinner from 'primevue/progressspinner';

defineProps({
  isLoading: {
    type: Boolean,
    required: true,
  },
});
</script>

<style scoped>
</style>