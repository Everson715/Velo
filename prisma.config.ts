import { defineConfig } from "prisma/config";
import { Pool } from "pg";
import { PrismaPg } from "@prisma/adapter-pg";

// Cria o pool de conexão apontando para o container do Docker
const pool = new Pool({ 
  connectionString: "postgresql://postgres:postgres@velo-postgres:5432/velo_identity?schema=public" 
});
const adapter = new PrismaPg(pool);

export default defineConfig({
  schema: "prisma/schema.prisma",
  migrations: {
    path: "prisma/migrations",
  },
  datasource: {
    adapter: adapter, // Informa ao Prisma para usar o driver direto do Postgres
  },
});