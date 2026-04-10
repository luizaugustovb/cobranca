# Configuração da Integração com a Viicio (WhatsApp)

Este documento descreve como configurar a plataforma **Viicio** para que o robô de WhatsApp consulte automaticamente as dívidas dos devedores quando eles digitam seu **CPF ou CNPJ**.

---

## 1. Pré-requisitos

| Item | Onde obter |
|---|---|
| Conta ativa na Viicio | [https://app.viicio.com.br](https://app.viicio.com.br) |
| Token de API da Viicio | Painel Viicio → Configurações → API |
| URL pública do sistema | Ex.: `https://seudominio.com.br` |
| `BOT_API_TOKEN` do sistema | Você mesmo define (senha secreta entre os dois sistemas) |

---

## 2. Configurar o `.env` do sistema

Abra o arquivo `.env` na raiz do projeto e preencha:

```dotenv
# Token da API Viicio (obtido no painel deles)
VIICIO_MASTER_TOKEN=seu_token_aqui

# URL base da Viicio (não alterar)
VIICIO_BASE_URL=https://api.viicio.com.br

# Token secreto que a Viicio usará para chamar este sistema
# Crie uma senha forte e anote — você vai configurar o mesmo valor na Viicio
BOT_API_TOKEN=uma_senha_forte_e_secreta_aqui
```

> **Importante:** Após editar o `.env`, execute `php artisan config:clear` para limpar o cache de configurações.

---

## 3. Endpoint de consulta do sistema

O sistema expõe **dois endpoints** (ambos fazem a mesma coisa — o segundo é alias do primeiro):

| Método | URL | Descrição |
|--------|-----|-----------|
| `POST` | `/api/bot/consultar-documento` | Endpoint principal — aceita CPF ou CNPJ |
| `POST` | `/api/bot/consultar-cpf` | Alias — mantido para compatibilidade |

### Autenticação

A Viicio deve enviar o `BOT_API_TOKEN` como **Bearer Token** no header:

```
Authorization: Bearer uma_senha_forte_e_secreta_aqui
```

### Corpo da requisição (JSON)

O campo pode ser qualquer um dos três nomes abaixo — o sistema aceita todos automaticamente:

```json
{ "documento": "12345678901" }
```

```json
{ "cpf": "12345678901" }
```

```json
{ "cnpj": "12345678000195" }
```

> O sistema remove pontos, traços e barras automaticamente. Funciona tanto com `"123.456.789-01"` quanto com `"12345678901"`.

### Respostas

**Devedor encontrado com dívida:**
```json
{
  "success": true,
  "found": true,
  "tipo": "CPF",
  "nome": "João da Silva",
  "total_titulos": 3,
  "valor_total": 1580.00,
  "valor_formatado": "R$ 1.580,00",
  "status": "Inadimplente",
  "whatsapp": "5511999999999",
  "message": "Olá *João da Silva*, identificamos *3 título(s)* em aberto totalizando *R$ 1.580,00*. Entre em contato para negociar suas dívidas."
}
```

**Devedor sem dívidas:**
```json
{
  "success": true,
  "found": true,
  "tipo": "CNPJ",
  "nome": "Empresa XYZ LTDA",
  "total_titulos": 0,
  "valor_total": 0.00,
  "valor_formatado": "R$ 0,00",
  "status": "Regular",
  "message": "Olá *Empresa XYZ LTDA*, não encontramos débitos em aberto em seu cadastro. 🎉"
}
```

**Não encontrado:**
```json
{
  "success": false,
  "found": false,
  "tipo": "CPF",
  "message": "Nenhum registro encontrado para este CPF."
}
```

**Documento inválido (nem CPF nem CNPJ):**
```json
{
  "success": false,
  "message": "Documento inválido. Informe um CPF (11 dígitos) ou CNPJ (14 dígitos)."
}
```

---

## 4. Configurar o fluxo na Viicio

### Passo 1 — Criar o fluxo de automação

1. Acesse o painel da Viicio.
2. Vá em **Automações** → **Novo Fluxo**.
3. Configure o gatilho: **"Quando o contato enviar uma mensagem"**.

### Passo 2 — Capturar o CPF/CNPJ digitado

1. Adicione uma ação de **"Enviar Mensagem"** com o texto:
   ```
   Olá! Para consultar suas pendências, por favor digite seu *CPF* ou *CNPJ* (apenas números):
   ```
2. Adicione uma ação **"Aguardar resposta do contato"** e salve a resposta em uma variável, ex.: `{{documento}}`.

### Passo 3 — Chamar o endpoint do sistema

Adicione uma ação de **"Requisição HTTP"**. Preencha os campos exatamente assim:

**URL:**
```
https://seudominio.com.br/api/bot/consultar-documento
```

**Método:**
```
POST
```

**Headers** (adicione dois headers clicando em "Adicionar Header"):

| Nome do Header | Valor |
|---|---|
| `Authorization` | `Bearer uma_senha_forte_e_secreta_aqui` |
| `Content-Type` | `application/json` |

> ⚠️ Não coloque aspas nem backticks no valor do `Content-Type` — apenas o texto `application/json`.

**Parâmetros:** deixe vazio.

**Body:**
```
{"documento": "{{documento}}"}
```

> Substitua `{{documento}}` pela variável que você salvou no Passo 2 com a resposta do contato.

---

**Salvar Resposta** — clique em "Salvar Resposta Da Requisição" e adicione os seguintes campos para poder usar o retorno nas próximas ações:

| Nome do Campo (variável) | Valor do Campo (caminho no JSON) |
|---|---|
| `resposta_message` | `message` |
| `resposta_found` | `found` |
| `resposta_nome` | `nome` |
| `resposta_valor` | `valor_formatado` |
| `resposta_status` | `status` |

> Após salvar, esses valores ficam disponíveis como variáveis no restante do fluxo com a sintaxe `{{resposta_message}}`, `{{resposta_found}}`, etc.

---

### Passo 4 — Enviar a resposta ao devedor

Adicione uma ação de **"Enviar Mensagem"** com o conteúdo:

```
{{resposta_message}}
```

A variável `{{resposta_message}}` já contém a mensagem pronta e formatada para WhatsApp (com `*negrito*`).

### Passo 5 — Tratar erros (recomendado)

Adicione uma **condição** após a requisição HTTP:

- **Se** `{{resposta_found}}` **for igual a** `false` → envie:
  ```
  Não encontramos seu cadastro. Verifique o número digitado ou entre em contato diretamente com nossa equipe.
  ```
- **Senão** → execute o Passo 4 (enviar `{{resposta_message}}`).

---

## 5. Testando a integração

### Teste via curl (terminal)

```bash
# Substituir os valores antes de executar
curl -X POST https://seudominio.com.br/api/bot/consultar-documento \
  -H "Authorization: Bearer uma_senha_forte_e_secreta_aqui" \
  -H "Content-Type: application/json" \
  -d '{"documento": "12345678901"}'
```

### Teste local (com servidor rodando em localhost:8000)

```bash
curl -X POST http://localhost:8000/api/bot/consultar-documento \
  -H "Authorization: Bearer uma_senha_forte_e_secreta_aqui" \
  -H "Content-Type: application/json" \
  -d '{"documento": "123.456.789-01"}'
```

---

## 6. Lógica de cálculo dos valores

O campo `valor_total` retornado considera a composição completa do título:

```
valor_total = valor_original + juros + multa + honorários - desconto
```

Somente títulos com `status = "aberto"` são contabilizados.

---

## 7. Segurança

- O `BOT_API_TOKEN` nunca deve ser compartilhado publicamente.
- Use HTTPS no servidor de produção para proteger os tokens em trânsito.
- O sistema rejeita qualquer requisição sem o token correto com erro `403 Forbidden`.
- O token deve ter pelo menos **32 caracteres** com letras, números e símbolos.

**Exemplo de token seguro** (gere um diferente):
```
php artisan key:generate --show
# ou use: openssl rand -base64 32
```
