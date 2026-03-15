<script setup>
import { ref, computed, onMounted } from "vue";

import Card from "primevue/card";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import Column from "primevue/column";

import ApiService, { PRODUCTS_ENDPOINT } from "@/services/ApiService.js";
import ToastService from "@/services/ToastService.js";
import { validateProduct, isFormValid, handleApiErrors } from "@/services/ValidationService.js";

const Toast = ToastService();

const products = ref([]);
const search = ref("");

const filteredProducts = computed(() => {
  const q = search.value.trim().toLowerCase();
  if (!q) return products.value;

  return products.value.filter((p) => {
    const value = `${p.id} ${p.nome} ${p.custo_medio} ${p.preco_venda} ${p.estoque}`.toLowerCase();
    return value.includes(q);
  });
});

const form = ref({
  nome: "",
  preco_venda: null,
});

const loading = ref(false);

// Verifica se o botão deve estar desabilitado (campos vazios)
const isFormEmpty = computed(() => {
  return !form.value.nome || form.value.preco_venda === null || form.value.preco_venda === undefined;
});

const loadProducts = async () => {
  try {
    const response = await ApiService.get(PRODUCTS_ENDPOINT);
    products.value = response.data.data ?? response.data;
  } catch (error) {
    Toast.error("Erro ao carregar produtos");
  }
};

const createProduct = async () => {
  // Validar antes de enviar
  const validationErrors = validateProduct(form.value);
  if (!isFormValid(validationErrors)) {
    // Mostrar todos os erros de validação
    Object.values(validationErrors).forEach((error) => {
      if (error) Toast.error(error);
    });
    return;
  }

  loading.value = true;

  try {
    await ApiService.post(PRODUCTS_ENDPOINT, form.value);

    Toast.success("Produto cadastrado com sucesso");

    form.value = {
      nome: "",
      preco_venda: null,
    };

    loadProducts();
  } catch (error) {
    handleApiErrors(error, Toast);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadProducts();
});
</script>

<template>
  <div class="grid">
    <!-- FORM PRODUTO -->
    <div class="col-12 md:col-5">
      <Card>
        <template #title> Novo Produto </template>

        <template #content>
          <div class="flex flex-column gap-3">
            <div class="flex flex-column gap-1">
              <label>Nome</label>
              <InputText v-model="form.nome" placeholder="Ex.: Mouse Gamer" />
            </div>

            <div class="flex flex-column gap-1">
              <label>Preço de Venda</label>
              <InputNumber
                v-model="form.preco_venda"
                mode="currency"
                currency="BRL"
                locale="pt-BR"
                placeholder="Ex.: R$ 199,90"
              />
            </div>

            <Button
              label="Cadastrar"
              icon="pi pi-save"
              :loading="loading"
              :disabled="isFormEmpty"
              @click="createProduct"
            />
          </div>
        </template>
      </Card>
    </div>

    <!-- TABELA PRODUTOS -->
    <div class="col-12 md:col-7">
      <Card>
        <template #title> Produtos </template>

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
              @click="loadProducts"
            />
          </div>

          <DataTable
            :value="filteredProducts"
            paginator
            :rows="10"
            responsiveLayout="scroll"
          >
            <Column field="id" header="ID" />

            <Column field="nome" header="Nome" />

            <Column field="custo_medio">
              <template #header>
                <div class="flex align-items-center gap-1">
                  <span class="font-semibold">Custo Médio</span>
                  <i
                    class="pi pi-question-circle text-sm"
                    v-tooltip.top="`Cálculo utilizado: (( estoque atual x custo médio atual ) + ( quantidade compra x custo unitário )) / novo estoque`"
                  />
                </div>
              </template>
            </Column>

            <Column field="preco_venda" header="Preço Venda" />

            <Column field="estoque" header="Estoque" />
          </DataTable>
        </template>
      </Card>
    </div>
  </div>
</template>
