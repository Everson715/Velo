import { defineConfig } from "prisma/config";

export default defineConfig({
  schema: "prisma/schema.prisma",
  migrations: {
    path: "prisma/migrations",
  },
  datasource: {
    // Trocado 'velo-postgres' por '127.0.0.1' para a sua máquina física alcançar o banco mapeado
    url: "postgresql://postgres:postgres@127.0.0.1:5432/velo_identity?schema=public",
  },
});
