<script setup>
import { ref, computed } from "vue";
import Popover from "primevue/popover";
import Button from "primevue/button";

import { useToastStore } from "@/stores/toastStore";

const toastStore = useToastStore();

const notifications = computed(() => toastStore.history.value);
const hasNotifications = computed(() => notifications.value.length > 0);

const clearNotifications = () => {
  toastStore.clear();
};

const popover = ref();
const toggle = (event) => {
  popover.value.toggle(event);
};

const timeAgo = (date) => {
  const seconds = Math.floor((Date.now() - new Date(date)) / 1000);

  if (seconds < 60) return `${seconds}s atrás`;

  const minutes = Math.floor(seconds / 60);
  if (minutes < 60) return `${minutes}min atrás`;

  const hours = Math.floor(minutes / 60);
  if (hours < 24) return `${hours}h atrás`;

  const days = Math.floor(hours / 24);
  return `${days}d atrás`;
};

const iconBySeverity = (severity) => {
  switch (severity) {
    case "success":
      return "pi pi-check-circle";

    case "warn":
      return "pi pi-exclamation-triangle";

    case "error":
      return "pi pi-times-circle";

    default:
      return "pi pi-info-circle";
  }
};

defineExpose({
  toggle,
});
</script>

<template>
  <Popover ref="popover" class="notifications-panel">
    <div class="notifications-header">
      <span>Histórico de Notificações</span>

      <Button
        label="Limpar"
        text
        size="small"
        @click="clearNotifications"
        v-if="hasNotifications"
      />
    </div>

    <div v-if="notifications.length === 0" class="notifications-empty">
      Nenhuma notificação
    </div>

    <div v-else class="notifications-list">
      <div
        v-for="(n, index) in notifications"
        :key="n.date"
        class="notification-item"
        :class="'notification-' + n.severity"
      >
        <div class="notification-icon">
          <i :class="iconBySeverity(n.severity)"></i>
        </div>

        <div class="notification-content">
          <div class="notification-summary">
            {{ n.summary }}
          </div>

          <div class="notification-detail">
            {{ n.detail }}
          </div>

          <div class="notification-date">
            {{ timeAgo(n.date) }}
          </div>
        </div>
      </div>
    </div>
  </Popover>
</template>

<style src="../assets/css/NotificationPanel.css" scoped></style>
