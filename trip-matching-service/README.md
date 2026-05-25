# Trip Matching Service 🗺️

O coração da operação do Velo. Este microsserviço é acionado quando um passageiro solicita uma corrida. Ele orquestra a lógica de pareamento (encontrar o motorista mais próximo), calcula estimativas de tarifa e acompanha o ciclo de vida da viagem (Solicitada -> Aceita -> Em Andamento -> Concluída).

## 🔧 Configurações

- **Stack:** Laravel 12 + Prisma ORM
- **Porta Externa:** `8002`
- **Database:** `velo_trips` no PostgreSQL compartilhado
- **Container Name:** `velo-php-trips`

## 📦 Prisma & Banco de Dados

Para rodar qualquer comando do Prisma, utilizamos a imagem Docker do Node.

*Não se esqueça de validar a sua `DATABASE_URL` no `.env` (remova as aspas duplas, se houver).*

**Comando de Migration:**

```bash
docker run --rm -v "$(pwd):/app" -w /app --network velo_velo_network -u 33:33 --env-file .env node:22-alpine npx prisma migrate dev
```
