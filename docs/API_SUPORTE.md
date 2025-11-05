# API de Suporte - Documentação

## Autenticação

Todos os endpoints da API de suporte requerem autenticação via token no header `Authorization`.

### Configuração do Token

1. Adicione a seguinte variável no arquivo `.env`:

```env
SUPORTE_API_TOKEN=seu_token_aqui
```

2. Gere um token seguro (recomenda-se usar um gerador de strings aleatórias):

```bash
# Exemplo de geração de token (Linux/Mac)
openssl rand -hex 32

# Ou usando PHP
php -r "echo bin2hex(random_bytes(32));"
```

3. Após adicionar o token no `.env`, certifique-se de limpar o cache de configuração:

```bash
php artisan config:clear
```

### Uso do Token

Envie o token no header `Authorization` de todas as requisições:

```
Authorization: Bearer seu_token_aqui
```

**Exemplo usando cURL - Criar Novo Ticket:**

```bash
curl -X POST http://painel.agendavoce.com.br/api/suporte/mensagem \
  -H "Authorization: Bearer seu_token_aqui" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "cliente@example.com",
    "assunto": "Dúvida sobre o sistema",
    "mensagem": "Preciso de ajuda com...",
    "prioridade": "alta"
  }'
```

**Exemplo usando cURL - Adicionar a Ticket Existente:**

```bash
curl -X POST http://painel.agendavoce.com.br/api/suporte/mensagem \
  -H "Authorization: Bearer seu_token_aqui" \
  -H "Content-Type: application/json" \
  -d '{
    "ticket_id": 123,
    "mensagem": "Nova mensagem no ticket",
    "prioridade": "alta"
  }'
```

**Exemplo usando JavaScript (fetch) - Criar Novo Ticket:**

```javascript
fetch('http://painel.agendavoce.com.br/api/suporte/mensagem', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer seu_token_aqui',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    email: 'cliente@example.com',
    assunto: 'Dúvida sobre o sistema',
    mensagem: 'Preciso de ajuda com...',
    prioridade: 'alta' // opcional: 'alta' ou 'normal'
  })
})
```

**Exemplo usando JavaScript (fetch) - Adicionar a Ticket Existente:**

```javascript
fetch('http://painel.agendavoce.com.br/api/suporte/mensagem', {
  method: 'POST',
  headers: {
    'Authorization': 'Bearer seu_token_aqui',
    'Content-Type': 'application/json'
  },
  body: JSON.stringify({
    ticket_id: 123,
    mensagem: 'Nova mensagem no ticket',
    prioridade: 'alta' // opcional: 'alta' ou 'normal'
  })
})
```

## Endpoints

### 1. Enviar Mensagem

**POST** `/api/suporte/mensagem`

Cria um novo ticket ou adiciona uma mensagem a um ticket existente.

#### Criar Novo Ticket

**Body:**
```json
{
  "email": "cliente@example.com",
  "assunto": "Assunto do ticket",
  "mensagem": "Mensagem do cliente",
  "prioridade": "alta"
}
```

**Campos obrigatórios:**
- `email`: Email do cliente
- `assunto`: Assunto do ticket
- `mensagem`: Mensagem do cliente (mínimo 10 caracteres)

**Campos opcionais:**
- `prioridade`: Prioridade do ticket - `"alta"` ou `"normal"` (padrão: `"normal"`)

#### Adicionar Mensagem a Ticket Existente

**Body:**
```json
{
  "ticket_id": 123,
  "mensagem": "Nova mensagem no ticket",
  "prioridade": "alta",
  "email": "cliente@example.com"
}
```

**Campos obrigatórios:**
- `ticket_id`: ID do ticket existente
- `mensagem`: Mensagem do cliente (mínimo 10 caracteres)

**Campos opcionais:**
- `email`: Email do cliente (usado para validação de segurança - deve corresponder ao email do ticket)
- `prioridade`: Prioridade do ticket - `"alta"` ou `"normal"` (padrão: `"normal"`)

**Nota:** Se você enviar `ticket_id`, não precisa enviar `email` e `assunto`. O sistema buscará o ticket pelo ID e adicionará a mensagem a ele. Se enviar o `email` junto com `ticket_id`, o sistema validará que o email corresponde ao ticket para maior segurança.

**Resposta:**
```json
{
  "success": true,
  "message": "Mensagem enviada com sucesso",
  "data": {
    "ticket_id": 1,
    "message_id": 1
  }
}
```

### 2. Listar Tickets de um Cliente

**GET** `/api/suporte/tickets/{email}`

Lista todos os tickets de um cliente específico.

**Resposta:**
```json
{
  "success": true,
  "data": [...]
}
```

### 3. Listar Mensagens de um Ticket

**GET** `/api/suporte/ticket/{ticket_id}/mensagens`

Lista todas as mensagens de um ticket específico.

**Resposta:**
```json
{
  "success": true,
  "data": {
    "ticket": {...},
    "messages": [...]
  }
}
```

### 4. Marcar Mensagem como Visualizada

**POST** `/api/suporte/mensagem/{message_id}/visualizar`

Marca uma mensagem específica como visualizada.

**Resposta:**
```json
{
  "success": true,
  "message": "Mensagem marcada como visualizada",
  "data": {
    "message_id": 1,
    "viewed_at": "2025-01-15 10:30:00"
  }
}
```

### 5. Marcar Ticket como Visualizado

**POST** `/api/suporte/ticket/{ticket_id}/visualizar`

Marca todas as mensagens do suporte em um ticket como visualizadas.

**Body:**
```json
{
  "email": "cliente@example.com"
}
```

**Resposta:**
```json
{
  "success": true,
  "message": "Mensagens marcadas como visualizadas",
  "data": {
    "ticket_id": 1,
    "mensagens_atualizadas": 3
  }
}
```

## Respostas de Erro

### Token não fornecido (401)
```json
{
  "success": false,
  "message": "Token de autorização não fornecido."
}
```

### Token inválido (401)
```json
{
  "success": false,
  "message": "Token de autorização inválido."
}
```

### Token não configurado (500)
```json
{
  "success": false,
  "message": "Token de API não configurado. Contate o administrador."
}
```

## Alterando o Token

Para alterar o token:

1. Edite o arquivo `.env` e altere o valor de `SUPORTE_API_TOKEN`
2. Execute: `php artisan config:clear`
3. Atualize todas as aplicações/clientes que utilizam a API com o novo token

**Importante:** Após alterar o token, todas as requisições antigas com o token anterior serão rejeitadas.

