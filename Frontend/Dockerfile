FROM node:20-alpine

# diretório da aplicação
WORKDIR /app

# copia arquivos de dependência
COPY package*.json ./

# instala dependências
RUN npm install

# copia restante do projeto
COPY . .

# expõe porta do vite
EXPOSE 5173

# inicia servidor dev
CMD ["npm", "run", "dev", "--", "--host"]