# Review & Rating Service ⭐

Microsserviço responsável pela reputação e segurança da comunidade. Após a conclusão de cada viagem, ele processa as avaliações e notas deixadas entre passageiros e motoristas, permitindo gerar métricas de confiabilidade para o sistema de matching.

## 🔧 Configurações

- **Stack:** Laravel 12 + Prisma ORM
- **Porta Externa:** `8003`
- **Database:** `velo_reviews` no PostgreSQL compartilhado
- **Container Name:** `velo-php-reviews`

## 📦 Prisma & Banco de Dados

Gerenciamento de banco isolado através do Prisma.

**Comando de Migration via Docker:**

```bash
docker run --rm -v "$(pwd):/app" -w /app --network velo_velo_network -u 33:33 --env-file .env node:22-alpine npx prisma migrate dev
```
