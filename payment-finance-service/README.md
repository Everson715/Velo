# Payment & Finance Service 💳

Controla toda a movimentação financeira da plataforma. Este microsserviço captura pagamentos de cartões de passageiros, registra os ganhos de cada motorista, gerencia descontos e as faturas pendentes, funcionando como o livro-caixa digital.

## 🔧 Configurações

- **Stack:** Laravel 12 + Prisma ORM
- **Porta Externa:** `8004`
- **Database:** `velo_payments` no PostgreSQL compartilhado
- **Container Name:** `velo-php-payments`

## 📦 Prisma & Banco de Dados

O banco de pagamentos é gerenciado via Prisma.

**Comando de Migration via Docker:**

```bash
docker run --rm -v "$(pwd):/app" -w /app --network velo_velo_network -u 33:33 --env-file .env node:22-alpine npx prisma migrate dev
```
