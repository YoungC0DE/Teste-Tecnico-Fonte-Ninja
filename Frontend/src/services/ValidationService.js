/**
 * Serviço de validação de formulários
 * Implementa as mesmas regras dos Requests do backend
 * Validação ocorre apenas no submit
 */

export const validateProduct = (product) => {
  const errors = {};

  // Nome: required, string, min:3, max:255
  if (!product.nome || product.nome.trim() === '') {
    errors.nome = 'O nome do produto é obrigatório';
  } else if (product.nome.length < 3) {
    errors.nome = 'O nome deve ter no mínimo 3 caracteres';
  } else if (product.nome.length > 255) {
    errors.nome = 'O nome deve ter no máximo 255 caracteres';
  }

  // Preço: required, numeric, min:0.01
  if (product.preco_venda === null || product.preco_venda === undefined || product.preco_venda === '') {
    errors.preco_venda = 'O preço de venda é obrigatório';
  } else if (isNaN(product.preco_venda) || product.preco_venda < 0.01) {
    errors.preco_venda = 'O preço deve ser no mínimo R$ 0,01';
  }

  return errors;
};

export const validatePurchase = (purchase) => {
  const errors = {};

  // Fornecedor: required, string, max:255
  if (!purchase.fornecedor || purchase.fornecedor.trim() === '') {
    errors.fornecedor = 'O nome do fornecedor é obrigatório';
  } else if (purchase.fornecedor.length > 255) {
    errors.fornecedor = 'O fornecedor deve ter no máximo 255 caracteres';
  }

  // Produtos: required, array, min:1
  if (!purchase.produtos || purchase.produtos.length === 0) {
    errors.produtos = 'Adicione pelo menos um produto';
  } else {
    // Validar cada item de produto
    const produtoErrors = [];
    purchase.produtos.forEach((item, index) => {
      const itemErrors = {};

      // ID: required, integer
      if (!item.id) {
        itemErrors.id = 'Selecione um produto';
      }

      // Quantidade: required, integer, min:1
      if (item.quantidade === null || item.quantidade === undefined || item.quantidade === '') {
        itemErrors.quantidade = 'A quantidade é obrigatória';
      } else if (!Number.isInteger(Number(item.quantidade)) || item.quantidade < 1) {
        itemErrors.quantidade = 'A quantidade deve ser no mínimo 1';
      }

      // Preço unitário: required, numeric, min:0.01
      if (item.preco_unitario === null || item.preco_unitario === undefined || item.preco_unitario === '') {
        itemErrors.preco_unitario = 'O preço unitário é obrigatório';
      } else if (isNaN(item.preco_unitario) || item.preco_unitario < 0.01) {
        itemErrors.preco_unitario = 'O preço deve ser no mínimo R$ 0,01';
      }

      if (Object.keys(itemErrors).length > 0) {
        produtoErrors[index] = itemErrors;
      }
    });

    if (produtoErrors.length > 0) {
      errors.produtosDetalhes = produtoErrors;
    }
  }

  return errors;
};

export const validateSale = (sale) => {
  const errors = {};

  // Cliente: required, string, max:255
  if (!sale.cliente || sale.cliente.trim() === '') {
    errors.cliente = 'O nome do cliente é obrigatório';
  } else if (sale.cliente.length > 255) {
    errors.cliente = 'O cliente deve ter no máximo 255 caracteres';
  }

  // Produtos: required, array, min:1
  if (!sale.produtos || sale.produtos.length === 0) {
    errors.produtos = 'Adicione pelo menos um produto';
  } else {
    // Validar cada item de produto
    const produtoErrors = [];
    sale.produtos.forEach((item, index) => {
      const itemErrors = {};

      // ID: required, integer
      if (!item.id) {
        itemErrors.id = 'Selecione um produto';
      }

      // Quantidade: required, integer, min:1
      if (item.quantidade === null || item.quantidade === undefined || item.quantidade === '') {
        itemErrors.quantidade = 'A quantidade é obrigatória';
      } else if (!Number.isInteger(Number(item.quantidade)) || item.quantidade < 1) {
        itemErrors.quantidade = 'A quantidade deve ser no mínimo 1';
      }

      // Preço unitário: required, numeric, min:0.01
      if (item.preco_unitario === null || item.preco_unitario === undefined || item.preco_unitario === '') {
        itemErrors.preco_unitario = 'O preço unitário é obrigatório';
      } else if (isNaN(item.preco_unitario) || item.preco_unitario < 0.01) {
        itemErrors.preco_unitario = 'O preço deve ser no mínimo R$ 0,01';
      }

      if (Object.keys(itemErrors).length > 0) {
        produtoErrors[index] = itemErrors;
      }
    });

    if (produtoErrors.length > 0) {
      errors.produtosDetalhes = produtoErrors;
    }
  }

  return errors;
};

/**
 * Verifica se um objeto de erros está vazio
 */
export const isFormValid = (errors) => {
  return Object.keys(errors).length === 0;
};

/**
 * Extrai mensagens de erro da resposta de erro da API
 * e as exibe como lista de toasts
 */
export const handleApiErrors = (errorResponse, toastService) => {
  if (!errorResponse.response || !errorResponse.response.data) {
    toastService.error('Erro ao enviar dados');
    return;
  }

  const data = errorResponse.response.data;

  // Se há objeto 'errors' com as mensagens de validação
  if (data.errors) {
    const errors = data.errors;
    
    // Itera sobre os erros e exibe cada um como um toast
    Object.keys(errors).forEach((field) => {
      const messages = errors[field];
      if (Array.isArray(messages)) {
        messages.forEach((message) => {
          toastService.error(message);
        });
      } else {
        toastService.error(messages);
      }
    });
  } else if (data.message) {
    toastService.error(data.message);
  } else {
    toastService.error('Erro ao enviar dados');
  }
};
