import { defineConfig, env } from "prisma/config";

export default defineConfig({
  schema: "prisma/schema.prisma",
  migrations: {
    path: "prisma/migrations",
  },
  datasource: {
    // O método env() do Prisma busca no arquivo .env sem disparar erros no TypeScript
    url: env("DATABASE_URL") || "postgresql://postgres:postgres@velo-postgres:5432/velo_identity?schema=public",
  },
});
