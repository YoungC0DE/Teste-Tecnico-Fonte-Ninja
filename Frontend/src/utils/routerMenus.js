import {
  PRODUCTS_ROUTER,
  PURCHASES_ROUTER,
  SALES_ROUTER,
} from "../router/index.js";

const routerMenus = [
  {
    label: "GERENCIAMENTO",
    items: [
      {
        label: "Produtos",
        icon: "pi pi-box",
        to: PRODUCTS_ROUTER,
      },
      {
        label: "Compras",
        icon: "pi pi-shopping-cart",
        to: PURCHASES_ROUTER,
      },
      {
        label: "Vendas",
        icon: "pi pi-dollar",
        to: SALES_ROUTER,
      },
    ],
  },
];

export default routerMenus;
