# API Financeiro Feito por: Igor Murilo Rossi 2°Informática

Este projeto disponibiliza endpoints para consultar e gerenciar categorias e lançamentos financeiros. Siga os passos abaixo para testar a API pelo Postman.

## 1. Inicie o servidor

No terminal, na pasta raiz do projeto, instale as dependências (se ainda não estiverem instaladas) e inicie o servidor PHP:

```bash
composer install
php -S 127.0.0.1:8000 -t public
```

Mantenha o terminal aberto enquanto utiliza o Postman. A URL base será `http://127.0.0.1:8000`.

## 2. Configure as requisições no Postman

1. Abra o Postman e crie uma requisição HTTP.
2. Selecione o método indicado para o endpoint.
3. Informe a URL completa usando a URL base e o caminho do endpoint.
4. Para requisições `POST` e `PUT` com dados, abra **Body**, selecione **raw** e escolha **JSON**. O Postman adicionará o cabeçalho `Content-Type: application/json`.
5. Clique em **Send** e confira o status HTTP e a resposta.

Os exemplos abaixo usam os dados iniciais definidos no código. A API não utiliza banco de dados; por isso, alterações não são persistidas e os dados iniciais são carregados novamente na próxima requisição.

## 3. Teste os endpoints de status

### Verificar se a API está disponível

- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/status`
- **Body:** nenhum
- **Resposta esperada:** `200 OK`

```json
{
  "status": "ok"
}
```

## 4. Teste os endpoints de categorias

### Listar categorias

- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/categorias`
- **Body:** nenhum
- **Resposta esperada:** `200 OK`, com a lista de categorias.

### Consultar uma categoria pelo ID

- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/categorias/1`
- **Body:** nenhum
- **Resposta esperada:** `200 OK`, com a categoria de ID `1`.

Para consultar outra categoria, substitua `1` pelo ID desejado. Um ID inexistente retorna `404 Not Found`:

```json
{
  "erro": "Categoria não encontrada"
}
```

### Criar uma categoria

- **Método:** `POST`
- **URL:** `http://127.0.0.1:8000/categorias`
- **Body (raw / JSON):**

```json
{
  "nome": "Transporte",
  "tipo": "Despesa"
}
```

- **Resposta esperada:** `201 Created`, com os dados da nova categoria e seu ID.

### Atualizar uma categoria

- **Método:** `PUT`
- **URL:** `http://127.0.0.1:8000/categorias/2`
- **Body (raw / JSON):**

```json
{
  "nome": "Moradia",
  "tipo": "Despesa"
}
```

- **Resposta esperada:** `200 OK`, com os dados atualizados.
- Para um ID inexistente, a resposta é `404 Not Found`.

### Excluir uma categoria

- **Método:** `DELETE`
- **URL:** `http://127.0.0.1:8000/categorias/2`
- **Body:** nenhum
- **Resposta esperada:** `204 No Content`.

## 5. Teste os endpoints de lançamentos

### Listar lançamentos

- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/lancamentos`
- **Body:** nenhum
- **Resposta esperada:** `200 OK`, com a lista de lançamentos.

### Consultar um lançamento pelo ID

- **Método:** `GET`
- **URL:** `http://127.0.0.1:8000/lancamentos/1`
- **Body:** nenhum
- **Resposta esperada:** `200 OK`, com o lançamento de ID `1`.

Para consultar outro lançamento, substitua `1` pelo ID desejado. Um ID inexistente retorna `404 Not Found`:

```json
{
  "erro": "Lançamento não encontrado"
}
```

### Criar um lançamento

- **Método:** `POST`
- **URL:** `http://127.0.0.1:8000/lancamentos`
- **Body (raw / JSON):**

```json
{
  "nome": "Conta de internet",
  "tipo": "Despesa",
  "valor": 120,
  "id_categorias": 2,
  "data": "2023-12-08"
}
```

`id_categorias` deve corresponder ao ID de uma categoria existente. Se não existir, a resposta será `400 Bad Request`:

```json
{
  "erro": "Categoria não encontrada"
}
```

Quando a categoria existe, a resposta esperada é `201 Created`, com os dados do lançamento.

### Atualizar um lançamento

- **Método:** `PUT`
- **URL:** `http://127.0.0.1:8000/lancamentos/1`
- **Body (raw / JSON):**

```json
{
  "nome": "Salário atualizado",
  "tipo": "Receita",
  "valor": 11000,
  "categorias": "Salário",
  "data": "2023-11-30"
}
```

Envie os campos que deseja atualizar. A resposta esperada é `200 OK`, com os dados do lançamento atualizado. Para um ID inexistente, a resposta é `404 Not Found`.

### Excluir um lançamento

- **Método:** `DELETE`
- **URL:** `http://127.0.0.1:8000/lancamentos/1`
- **Body:** nenhum
- **Resposta esperada:** `204 No Content`.
