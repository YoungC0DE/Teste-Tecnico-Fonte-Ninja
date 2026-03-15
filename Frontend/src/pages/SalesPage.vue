<script setup>
import { ref, computed, onMounted } from "vue";

import Card from "primevue/card";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Select from "primevue/select";

import ApiService, { SALES_ENDPOINT, PRODUCTS_ENDPOINT } from "@/services/ApiService.js";
import ToastService from "@/services/ToastService.js";
import { validateSale, isFormValid, handleApiErrors } from "@/services/ValidationService.js";

const Toast = ToastService();

const sales = ref([]);
const products = ref([]);
const search = ref("");

const filteredSales = computed(() => {
  const q = search.value.trim().toLowerCase();
  if (!q) return sales.value;

  return sales.value.filter((s) =>
    JSON.stringify(s).toLowerCase().includes(q)
  );
});

const form = ref({
  cliente: "",
  produtos: [
    {
      id: null,
      quantidade: null,
      preco_unitario: null,
    },
  ],
});

const loading = ref(false);

// Verifica se o formulário está vazio para desabilitar o botão
const isFormEmpty = computed(() => {
  return (
    !form.value.cliente ||
    !form.value.produtos ||
    form.value.produtos.length === 0 ||
    form.value.produtos.some(
      (p) => !p.id || p.quantidade === null || p.quantidade === undefined || p.preco_unitario === null || p.preco_unitario === undefined
    )
  );
});

const loadProducts = async () => {
  try {
    const response = await ApiService.get(PRODUCTS_ENDPOINT);
    const items = response.data.data ?? response.data;
    products.value = items.map((p) => ({ label: p.nome, value: p.id }));
  } catch (error) {
    Toast.error("Erro ao carregar produtos");
  }
};

const loadSales = async () => {
  try {
    const response = await ApiService.get(SALES_ENDPOINT);
    sales.value = response.data.data ?? response.data;
  } catch (error) {
    Toast.error("Erro ao carregar vendas");
  }
};

const addProductLine = () => {
  form.value.produtos.push({ id: null, quantidade: null, preco_unitario: null });
};

const removeProductLine = (index) => {
  form.value.produtos.splice(index, 1);
};

const createSale = async () => {
  // Validar antes de enviar
  const validationErrors = validateSale(form.value);
  if (!isFormValid(validationErrors)) {
    // Mostrar todos os erros de validação
    Object.values(validationErrors).forEach((error) => {
      if (error && typeof error === 'string') Toast.error(error);
    });
    return;
  }

  loading.value = true;

  try {
    await ApiService.post(SALES_ENDPOINT, form.value);

    Toast.success("Venda registrada com sucesso");

    form.value = {
      cliente: "",
      produtos: [
        {
          id: null,
          quantidade: null,
          preco_unitario: null,
        },
      ],
    };

    loadSales();
  } catch (error) {
    handleApiErrors(error, Toast);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadProducts();
  loadSales();
});
</script>

<template>
  <div class="grid">
    <div class="col-12 md:col-5">
      <Card>
        <template #title>Nova Venda</template>

        <template #content>
          <div class="flex flex-column gap-3">
            <div class="flex flex-column gap-1">
              <label>Cliente</label>
              <InputText
                v-model="form.cliente"
                placeholder="Ex.: João da Silva"
              />
            </div>

            <div class="flex flex-column gap-3">
              <div
                v-for="(item, index) in form.produtos"
                :key="index"
                class="card p-3"
              >
                <div class="flex flex-wrap gap-3 align-items-end">
                  <div class="flex flex-column gap-1" style="flex: 1 1 200px;">
                    <label>Produto</label>
                    <Select
                      v-model="item.id"
                      :options="products"
                      optionLabel="label"
                      optionValue="value"
                      placeholder="Selecione o produto"
                    />
                  </div>

                  <div class="flex flex-column gap-1" style="flex: 1 1 120px;">
                    <label>Quantidade</label>
                    <InputNumber
                      v-model="item.quantidade"
                      showButtons
                      :min="1"
                      placeholder="Ex.: 2"
                    />
                  </div>

                  <div class="flex flex-column gap-1" style="flex: 1 1 160px;">
                    <label>Preço Unitário</label>
                    <InputNumber
                      v-model="item.preco_unitario"
                      mode="currency"
                      currency="BRL"
                      locale="pt-BR"
                      showButtons
                      :min="0"
                      placeholder="Ex.: R$ 250,00"
                    />
                  </div>

                  <div class="flex align-items-end">
                    <Button
                      icon="pi pi-trash"
                      severity="danger"
                      rounded
                      text
                      @click="removeProductLine(index)"
                    />
                  </div>
                </div>
              </div>

              <Button
                label="Adicionar produto"
                icon="pi pi-plus"
                severity="secondary"
                @click="addProductLine"
              />
            </div>

            <Button
              label="Registrar venda"
              icon="pi pi-save"
              :loading="loading"
              :disabled="isFormEmpty"
              @click="createSale"
            />
          </div>
        </template>
      </Card>
    </div>

    <div class="col-12 md:col-7">
      <Card>
        <template #title>Vendas</template>

        <template #content>
          <div class="flex flex-column md:flex-row md:align-items-center md:justify-content-between gap-3 mb-4">
            <InputText
              v-model="search"
              placeholder="Pesquisar..."
              class="w-full md:w-6"
            />
            <Button
              icon="pi pi-refresh"
              label="Atualizar"
              class="p-button-text"
              @click="loadSales"
            />
          </div>

          <DataTable
            :value="filteredSales"
            paginator
            :rows="10"
            responsiveLayout="scroll"
          >
            <Column field="id" header="ID" />
            <Column field="cliente" header="Cliente" />
            <Column field="valor_total" header="Valor Total" />
            <Column field="created_at" header="Data" />
          </DataTable>
        </template>
      </Card>
    </div>
  </div>
</template>