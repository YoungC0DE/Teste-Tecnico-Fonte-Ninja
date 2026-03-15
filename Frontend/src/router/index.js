import { createRouter, createWebHistory } from "vue-router"

import ProductsPage from "@/pages/ProductsPage.vue"
import PurchasesPage from "@/pages/PurchasesPage.vue"
import SalesPage from "@/pages/SalesPage.vue"

export const PRODUCTS_ROUTER = 'products';
export const PURCHASES_ROUTER = 'purchases';
export const SALES_ROUTER = 'sales';

const routes = [
  {
    path: "/",
    name: PRODUCTS_ROUTER,
    component: ProductsPage
  },
  {
    path: "/compras",
    name: PURCHASES_ROUTER,
    component: PurchasesPage
  },
  {
    path: "/vendas",
    name: SALES_ROUTER,
    component: SalesPage
  }
]

const router = createRouter({
  history: createWebHistory(),
  routes
})

export default router;