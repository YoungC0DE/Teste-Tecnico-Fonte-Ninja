# ERP Estoque

Projeto **fullstack** para gerenciamento de **produtos, compras e vendas**, composto por **Backend (API REST)** e **Frontend**, orquestrados com **Docker**.

O sistema permite registrar operações de estoque e acompanhar o impacto dessas movimentações no **custo médio, estoque disponível e lucro obtido nas vendas**.

> ⚠️ Este projeto foi desenvolvido e testado em ambiente **WSL (Windows Subsystem for Linux)** utilizando **Ubuntu no Windows**.

---

# 🌐 Acesso ao Projeto (Ambiente de Homologação)

O projeto também está disponível online para testes:

Frontend  
https://teste-tecnico-fonte-ninja.vercel.app/

API  
https://teste-tecnico-fonte-ninja.onrender.com

Documentação da API  
https://teste-tecnico-fonte-ninja.onrender.com/docs

> ⚠️ O primeiro acesso à API pode levar alguns segundos, pois o Render hiberna serviços gratuitos após período de inatividade.

---

# 📦 Estrutura do Projeto
```
.
├── Backend
│   ├── app
│   ├── routes
│   ├── Dockerfile
│   ├── .env.example
│   └── ...
│
├── Frontend
│   ├── src
│   ├── Dockerfile
│   ├── .env.example
│   └── ...
│
├── docker-compose.yml
├── import_file_endpoints_insomnia.json
└── README.md
```
---

# 🛠 Tecnologias Utilizadas

## Backend

- Laravel 12
- PHP 8.4
- PostgreSQL
- Redis

## Frontend

- Vue 3
- PrimeVue

## Ferramentas

- Docker
- Docker Compose
- Insomnia / Postman

---

# 🚀 Executando o Projeto Localmente

## 1️⃣ Clonar o repositório

git clone https://github.com/YoungC0DE/Teste-Tecnico-Fonte-Ninja.git  
cd Teste-Tecnico-Fonte-Ninja

---

## 2️⃣ Criar os arquivos de ambiente

Tanto o **Frontend** quanto o **Backend** possuem arquivos `.env`.

Crie os arquivos a partir dos exemplos fornecidos:

Backend/.env.example  → Backend/.env  
Frontend/.env.example → Frontend/.env

---

## 3️⃣ Subir os containers

Na pasta **raiz do projeto**, execute:

docker compose up -d

Esse comando irá iniciar todos os serviços necessários:

- API Laravel
- Banco PostgreSQL
- Redis
- Frontend

---

## 4️⃣ Instalar dependências do Backend

Após subir os containers, acesse o container da API:

docker compose exec api bash

Dentro do container, execute:
```
composer install -o
```

---

# 📚 Documentação da API

A documentação da API é gerada automaticamente utilizando **Scribe**.

Ambiente local

http://localhost:8000/docs

Ambiente de homologação

https://teste-tecnico-fonte-ninja.onrender.com/docs

Nesta página é possível visualizar:

- Lista de todos os endpoints
- Parâmetros de requisição
- Exemplos de payload
- Exemplos de respostas da API

A documentação é gerada diretamente a partir dos **Controllers** e **Requests**, garantindo que esteja sempre sincronizada com o código da aplicação.

---

# 🔎 Testando a API

Para testar os endpoints manualmente:

1. Abra o **Insomnia** ou **Postman**
2. Importe o arquivo:

import_file_endpoints_insomnia.json

O arquivo está localizado **na raiz do projeto** e contém uma coleção com os endpoints disponíveis na API.

---

## Variáveis de ambiente no Insomnia

Caso utilize o **Insomnia em Scratch Pad (modo local/offline)**, pode ser necessário registrar manualmente as variáveis de ambiente.

Abra:

Manage Environments

E adicione:
```
{
  "url_base_local": "http://localhost:8000/api/v1",
  "url_base_hml": "https://teste-tecnico-fonte-ninja.onrender.com/api/v1"
}
```
---

# 🧪 Testando o Frontend

Ambiente local

http://localhost:5173

Ambiente de homologação

https://teste-tecnico-fonte-ninja.vercel.app/

A interface permite:

- Cadastro de produtos
- Visualização do estoque
- Registro de compras
- Registro de vendas

---

# ⚙️ Funcionalidades da API

A API fornece endpoints para:

- Cadastro de produtos
- Registro de compras
- Registro de vendas
- Controle de estoque
- Cálculo automático de custo médio
- Cálculo de lucro nas vendas

---

# 🧩 Estrutura da API

A API segue o padrão **REST** e possui:

- Controllers
- Requests para validação
- Models
- Versionamento de rotas (`/api/v1`)

---

# 🎨 Interface do Frontend

O layout do frontend foi **inspirado no template Sakai**, disponibilizado pelo **PrimeVue**, adaptado para atender às necessidades deste projeto.

O objetivo foi manter uma interface **simples, limpa e focada em produtividade**, comum em sistemas administrativos e ERPs.

---

# 🤖 Uso de Inteligência Artificial no desenvolvimento

Durante o desenvolvimento do projeto foram utilizadas ferramentas de apoio baseadas em **Inteligência Artificial**, incluindo:

- GitHub Copilot
- ChatGPT
- Cursor

Essas ferramentas foram utilizadas como **suporte para produtividade, revisão de código e esclarecimento de dúvidas técnicas**, sem geração automática integral do projeto.

---

# 📄 Licença

Projeto desenvolvido para **fins educacionais e avaliação técnica**.
