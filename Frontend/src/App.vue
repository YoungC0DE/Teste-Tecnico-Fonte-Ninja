<script setup>
import { ref, computed, onMounted, onUnmounted, inject } from "vue";
import { RouterView, RouterLink, useRoute } from "vue-router";
import Badge from "primevue/badge";

import Toast from "primevue/toast";
import Button from "primevue/button";
import LoadingOverlay from "@/components/LoadingOverlay.vue";
import NotificationPanel from "@/components/NotificationPanel.vue";
import { useToastStore } from "@/stores/toastStore";
import RouteMenus from "@/utils/routerMenus.js";

const toastStore = useToastStore();
const notificationPanel = ref();
const openNotifications = (event) => {
  notificationPanel.value.toggle(event);
};

const isLoading = inject("isLoading");
const route = useRoute();
const sidebarCollapsed = ref(false);
const sidebarVisible = ref(false);
const windowWidth = ref(window.innerWidth); 
const isMobile = computed(() => windowWidth.value < 1024);

const isActiveRoute = (to) => route.name === to;
const updateWidth = () => {
  windowWidth.value = window.innerWidth;

  if (windowWidth.value >= 1024) {
    sidebarVisible.value = true;
  }
};


onMounted(() => {
  updateWidth();
  window.addEventListener("resize", updateWidth);
});

onUnmounted(() => {
  window.removeEventListener("resize", updateWidth);
});

const toggleSidebar = () => {
  if (isMobile.value) {
    sidebarVisible.value = !sidebarVisible.value;
    return;
  }

  sidebarCollapsed.value = !sidebarCollapsed.value;
};

const isDark = ref(localStorage.getItem("theme") === "dark");

const toggleTheme = () => {
  isDark.value = !isDark.value;
  document.documentElement.classList.toggle("dark", isDark.value);
  localStorage.setItem("theme", isDark.value ? "dark" : "light");
};

const closeSidebar = () => {
  if (isMobile.value) sidebarVisible.value = false;
};
</script>

<template>
  <div class="layout-wrapper">
    <!-- TOPBAR -->
    <header class="layout-topbar">
      <div class="layout-topbar-left">
        <button class="menu-button" @click="toggleSidebar">
          <i class="pi pi-bars"></i>
        </button>

        <div class="flex align-items-center gap-2">
          <i class="pi pi-box"></i>
          <div class="app-title">ERP Estoque</div>
        </div>
      </div>

      <div class="layout-topbar-actions">
        <Button
          :icon="isDark ? 'pi pi-sun' : 'pi pi-moon'"
          text
          rounded
          v-tooltip.bottom="'Alternar tema'"
          @click="toggleTheme"
        />
        <div class="notification-wrapper">
          <Button icon="pi pi-bell" text rounded @click="openNotifications" v-tooltip.bottom="'Histórico de notificações'" />

          <Badge
            v-if="toastStore.history.length > 0"
            severity="danger"
            class="notification-badge"
          />
        </div>
      </div>
    </header>

    <!-- BODY -->
    <div class="layout-body">
      <!-- SIDEBAR -->
      <aside
        class="layout-sidebar"
        :class="{
          'layout-sidebar-open': sidebarVisible || !isMobile,
          'layout-sidebar-collapsed': !isMobile && sidebarCollapsed,
        }"
      >
        <div class="layout-menu">
          <template v-for="section in RouteMenus" :key="section.label">
            <div class="layout-menu-section">
              <div
                v-if="!sidebarCollapsed || isMobile"
                class="layout-menu-section-label"
              >
                {{ section.label }}
              </div>

              <ul>
                <li v-for="item in section.items" :key="item.label">
                  <RouterLink
                    :to="{ name: item.to }"
                    class="layout-menu-link"
                    :class="{ active: isActiveRoute(item.to) }"
                    v-tooltip.right="
                      sidebarCollapsed && !isMobile ? item.label : null
                    "
                    @click="closeSidebar"
                  >
                    <i
                      :class="[
                        item.icon,
                        { 'text-primary': isActiveRoute(item.to) },
                      ]"
                    ></i>

                    <span
                      v-if="!sidebarCollapsed || isMobile"
                      :class="{ 'text-primary': isActiveRoute(item.to) }"
                    >
                      {{ item.label }}
                    </span>
                  </RouterLink>
                </li>
              </ul>
            </div>
          </template>
        </div>
      </aside>

      <!-- CONTENT -->
      <main class="layout-main">
        <div class="layout-content">
          <RouterView />
        </div>
      </main>
    </div>

    <!-- MOBILE MASK -->
    <div
      v-if="sidebarVisible && isMobile"
      class="sidebar-mask"
      @click="closeSidebar"
    />

    <Toast />
    <NotificationPanel ref="notificationPanel" />
    <LoadingOverlay :is-loading="isLoading" />
  </div>
</template>
