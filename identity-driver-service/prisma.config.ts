import { defineConfig } from "prisma/config";

// Verifica de forma segura se o 'process' do Node existe no ambiente atual
const databaseUrl = typeof process !== "undefined" ? process.env.DATABASE_URL : undefined;

export default defineConfig({
  schema: "prisma/schema.prisma",
  migrations: {
    path: "prisma/migrations",
  },
  datasource: {
    // Se tiver a variável no .env local, usa ela. Se não tiver, usa o fallback do Docker
    url: databaseUrl || "postgresql://postgres:postgres@velo-postgres:5432/velo_identity?schema=public",
  },
});
