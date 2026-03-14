# ERP Inventory System

Projeto fullstack para gerenciamento de **produtos, compras e vendas**, composto por **Frontend** e **Backend**, orquestrados via Docker.

## 📦 Estrutura do Projeto

```
.
├── Backend
│   ├── app
│   ├── routes
│   ├── Docker
│   ├── .env.example
│   └── ...
│
├── Frontend
│   ├── src
│   ├── Docker
│   ├── .env.example
│   └── ...
│
├── docker-compose.yml
├── import_file_endpoints_insomnia.json
└── README.md
```

## 🛠 Tecnologias

### Backend

* Laravel 12
* PHP 8.4
* MySQL
* Docker

### Frontend

* Vue 3
* Docker

### Ferramentas

* Docker
* Docker Compose
* Insomnia / Postman

---

# 🚀 Como executar o projeto

## 1️⃣ Clonar o repositório

```
git clone https://github.com/YoungC0DE/Teste-Tecnico-Fonte-Ninja.git
cd Teste-Tecnico-Fonte-Ninja
```

---

## 2️⃣ Criar os arquivos de ambiente

Tanto o **Frontend** quanto o **Backend** possuem arquivos `.env`.

Crie os arquivos com base nos exemplos:

```
Backend/.env.example  → Backend/.env
Frontend/.env.example → Frontend/.env
```

---

## 3️⃣ Subir os containers

Na pasta **raiz do projeto**, execute:

```
docker-compose up -d
```

Isso irá subir todos os serviços necessários do projeto.

---

## 4️⃣ Instalar dependências do backend

Após subir os containers, entre no container da API:

```
docker compose exec api bash
```

Dentro do container, execute:

```
composer install -o
```

---

# 🔎 Testando a API

Para testar os endpoints da API:

1. Abra o **Insomnia** ou **Postman**
2. Importe o arquivo:

```
import_file_endpoints_insomnia.json
```

Esse arquivo está localizado **na raiz do projeto** e contém todos os endpoints da API.

---

# 📚 Funcionalidades da API

A API permite:

* Cadastro de produtos
* Registro de compras
* Registro de vendas
* Controle de estoque
* Cálculo de lucro

---

# 🧪 Estrutura da API

A API segue o padrão **REST** e utiliza:

* Controllers
* Requests para validação
* Models
* Versionamento de rotas (`/api/v1`)

## 📚 Documentação da API (Backend)

A documentação completa da API é gerada automaticamente utilizando o **Scribe**.

Após subir os containers do projeto, a documentação pode ser acessada diretamente no navegador pela url **http://localhost:8000/docs**

Nessa página você encontrará:

- Lista de todos os endpoints disponíveis
- Parâmetros de requisição
- Exemplos de payloads
- Exemplos de respostas da API

A documentação é gerada automaticamente a partir dos **Controllers** e **Requests** do backend, garantindo que os exemplos e parâmetros estejam sempre atualizados com o código da aplicação.

# 📄 Licença

Projeto criado para fins educacionais / avaliação técnica.