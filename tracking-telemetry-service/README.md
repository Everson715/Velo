# Tracking & Telemetry Service 📡

Serviço de alta performance especializado no monitoramento contínuo das rotas e coordenadas GPS. Utilizando **Laravel Reverb**, atua com WebSockets para enviar e receber atualizações em tempo real da posição dos motoristas para os aplicativos clientes.

## 🔧 Configurações

- **Stack:** Laravel 12 + Laravel Reverb + Prisma ORM
- **Porta Externa:** `8005`
- **Database:** `velo_tracking` no PostgreSQL compartilhado
- **Container Name:** `velo-php-tracking`

## 📦 Prisma & Banco de Dados

Comandos para estruturação e versionamento do banco via container:

**Comando de Migration via Docker:**

```bash
docker run --rm -v "$(pwd):/app" -w /app --network velo_velo_network -u 33:33 --env-file .env node:22-alpine npx prisma migrate dev
```
