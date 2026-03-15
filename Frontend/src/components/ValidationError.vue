<template>
  <div v-if="error" class="validation-error">
    <i class="pi pi-exclamation-circle"></i>
    <span>{{ error }}</span>
  </div>
</template>

<script setup>
defineProps({
  error: {
    type: String,
    default: null,
  },
});
</script>

<style scoped>
.validation-error {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 6px;
  background-color: rgba(239, 68, 68, 0.1);
  color: var(--p-red-500);
  font-size: 0.9rem;
  border-left: 3px solid var(--p-red-500);
}

.validation-error i {
  flex-shrink: 0;
}
</style>
