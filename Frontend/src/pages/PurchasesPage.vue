<script setup>
import { ref, computed, onMounted } from "vue";

import Card from "primevue/card";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Button from "primevue/button";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import Select from "primevue/select";

import ApiService, { PURCHASES_ENDPOINT, PRODUCTS_ENDPOINT } from "@/services/ApiService.js";
import ToastService from "@/services/ToastService.js";
import { validatePurchase, isFormValid, handleApiErrors } from "@/services/ValidationService.js";

const Toast = ToastService();

const purchases = ref([]);
const products = ref([]);
const search = ref("");

const filteredPurchases = computed(() => {
  const q = search.value.trim().toLowerCase();
  if (!q) return purchases.value;

  return purchases.value.filter((p) =>
    JSON.stringify(p).toLowerCase().includes(q)
  );
});

const form = ref({
  fornecedor: "",
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
    !form.value.fornecedor ||
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

const loadPurchases = async () => {
  try {
    const response = await ApiService.get(PURCHASES_ENDPOINT);
    purchases.value = response.data.data ?? response.data;
  } catch (error) {
    Toast.error("Erro ao carregar compras");
  }
};

const addProductLine = () => {
  form.value.produtos.push({ id: null, quantidade: null, preco_unitario: null });
};

const removeProductLine = (index) => {
  form.value.produtos.splice(index, 1);
};

const createPurchase = async () => {
  // Validar antes de enviar
  const validationErrors = validatePurchase(form.value);
  if (!isFormValid(validationErrors)) {
    // Mostrar todos os erros de validação
    Object.values(validationErrors).forEach((error) => {
      if (error && typeof error === 'string') Toast.error(error);
    });
    return;
  }

  loading.value = true;

  try {
    await ApiService.post(PURCHASES_ENDPOINT, form.value);

    Toast.success("Compra registrada com sucesso");

    form.value = {
      fornecedor: "",
      produtos: [
        {
          id: null,
          quantidade: null,
          preco_unitario: null,
        },
      ],
    };

    loadPurchases();
  } catch (error) {
    handleApiErrors(error, Toast);
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  loadProducts();
  loadPurchases();
});
</script>

<template>
  <div class="grid">
    <div class="col-12 md:col-5">
      <Card>
        <template #title>Nova Compra</template>

        <template #content>
          <div class="flex flex-column gap-3">
            <div class="flex flex-column gap-1">
              <label>Fornecedor</label>
              <InputText
                v-model="form.fornecedor"
                placeholder="Ex.: Fornecedor XPTO Ltda"
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
                      placeholder="Ex.: 10"
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
                      placeholder="Ex.: R$ 15,00"
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
              label="Registrar compra"
              icon="pi pi-save"
              :loading="loading"
              :disabled="isFormEmpty"
              @click="createPurchase"
            />
          </div>
        </template>
      </Card>
    </div>

    <div class="col-12 md:col-7">
      <Card>
        <template #title>Compras</template>

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
              @click="loadPurchases"
            />
          </div>

          <DataTable
            :value="filteredPurchases"
            paginator
            :rows="10"
            responsiveLayout="scroll"
          >
            <Column field="id" header="ID" />
            <Column field="fornecedor" header="Fornecedor" />
            <Column field="valor_total" header="Valor Total" />
            <Column field="created_at" header="Data" />
          </DataTable>
        </template>
      </Card>
    </div>
  </div>
</template>