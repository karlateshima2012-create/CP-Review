CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "tenant_id" varchar,
  "role" varchar not null default 'owner',
  "last_login_at" datetime
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "clientes"(
  "id" varchar not null,
  "user_id" integer,
  "nome_empresa" varchar not null,
  "email" varchar,
  "telefone_whatsapp" varchar,
  "line_user_id" varchar,
  "slug" varchar not null,
  "pais" varchar check("pais" in('br', 'jp')) not null default 'br',
  "canal_notificacao" varchar check("canal_notificacao" in('email', 'whatsapp', 'line')) not null default 'email',
  "plano" varchar not null default 'standard',
  "ativo" tinyint(1) not null default '1',
  "data_ativacao" datetime,
  "msg_boas_vindas_br" varchar not null default 'Olá! 👋 Vamos avaliar sua experiência?',
  "msg_pergunta_nota_br" varchar not null default 'Como foi sua experiência hoje?',
  "msg_agradecimento_alta_br" varchar not null default 'Que ótimo! Fico muito feliz! 🎉',
  "msg_agradecimento_baixa_br" varchar not null default 'Lamento que sua experiência não tenha sido boa. Agradecemos sua honestidade. 🙏',
  "msg_boas_vindas_jp" varchar not null default 'ご来店ありがとうございます。',
  "msg_pergunta_nota_jp" varchar not null default '本日の体験はいかがでしたでしょうか？',
  "msg_agradecimento_alta_jp" varchar not null default '高評価をいただき、誠にありがとうございます。励みになります！',
  "msg_agradecimento_baixa_jp" varchar not null default '貴重なご意見をいただき、ありがとうございます。改善に努めてまいります。',
  "created_at" datetime,
  "updated_at" datetime,
  "google_maps_link" varchar,
  "valor_mensal" numeric not null default '0',
  "trial_ends_at" datetime,
  "status" varchar not null default 'ativo',
  "qr_logo_path" varchar,
  "qr_color" varchar not null default '#7C3AED',
  "logo_path" varchar,
  "cover_path" varchar,
  "motivos_problema" text,
  "pack_idioma" varchar check("pack_idioma" in('pt_ja', 'ja_en')) not null default 'pt_ja',
  foreign key("user_id") references "users"("id") on delete cascade,
  primary key("id")
);
CREATE UNIQUE INDEX "clientes_slug_unique" on "clientes"("slug");
CREATE TABLE IF NOT EXISTS "avaliacoes"(
  "id" varchar not null,
  "tenant_id" varchar not null,
  "nota" integer not null,
  "feedback" text,
  "problema" varchar,
  "nome_cliente" varchar not null default 'Anônimo',
  "tipo_contato" varchar,
  "contato_valor" varchar,
  "token_resposta" varchar,
  "resposta_dono" text,
  "respondida_em" datetime,
  "primeira_visita" tinyint(1) not null default '0',
  "periodo_visita" varchar,
  "foto_problema" varchar,
  "resolvido" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("tenant_id") references "clientes"("id") on delete cascade,
  primary key("id")
);
CREATE UNIQUE INDEX "avaliacoes_token_resposta_unique" on "avaliacoes"(
  "token_resposta"
);
CREATE TABLE IF NOT EXISTS "transacoes"(
  "id" integer primary key autoincrement not null,
  "transacao_id" varchar not null,
  "empresa" varchar not null,
  "email" varchar not null,
  "telefone" varchar not null,
  "line_id" varchar,
  "plano" varchar not null,
  "valor" numeric not null,
  "slug" varchar,
  "pais" varchar check("pais" in('br', 'jp')) not null default 'br',
  "canal" varchar check("canal" in('email', 'whatsapp', 'line')) not null default 'email',
  "tenant_id" integer,
  "status" varchar check("status" in('pendente', 'aprovado', 'rejeitado')) not null default 'pendente',
  "created_at" datetime,
  "updated_at" datetime,
  "pack_idioma" varchar check("pack_idioma" in('pt_ja', 'ja_en')) not null default 'pt_ja',
  foreign key("tenant_id") references "clientes"("id")
);
CREATE UNIQUE INDEX "transacoes_transacao_id_unique" on "transacoes"(
  "transacao_id"
);
CREATE INDEX "idx_tenant_created_at" on "avaliacoes"(
  "tenant_id",
  "created_at"
);
CREATE INDEX "idx_tenant_resolvido" on "avaliacoes"("tenant_id", "resolvido");
CREATE INDEX "idx_tenant_nota" on "avaliacoes"("tenant_id", "nota");
CREATE INDEX "idx_tenant_id" on "transacoes"("tenant_id");
CREATE TABLE IF NOT EXISTS "daily_metrics_summary"(
  "tenant_id" varchar not null,
  "metric_date" date not null,
  "period" varchar check("period" in('lunch', 'dinner', 'other', 'all')) not null,
  "total_reviews" integer not null default '0',
  "positive_count" integer not null default '0',
  "negative_count" integer not null default '0',
  "rating_1" integer not null default '0',
  "rating_2" integer not null default '0',
  "rating_3" integer not null default '0',
  "rating_4" integer not null default '0',
  "rating_5" integer not null default '0',
  "avg_rating" numeric not null default '0',
  "first_visit_count" integer not null default '0',
  "returning_count" integer not null default '0',
  "top_issues" text,
  "aggregated_at" datetime not null default CURRENT_TIMESTAMP,
  primary key("tenant_id", "metric_date", "period")
);
CREATE INDEX "idx_tenant_date" on "daily_metrics_summary"(
  "tenant_id",
  "metric_date"
);
CREATE TABLE IF NOT EXISTS "audit_logs"(
  "id" integer primary key autoincrement not null,
  "user_id" varchar,
  "action" varchar not null,
  "details" text,
  "ip_address" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE TABLE IF NOT EXISTS "notificacoes_logs"(
  "id" varchar not null,
  "tenant_id" varchar not null,
  "avaliacao_id" varchar,
  "canal" varchar not null,
  "destinatario" varchar not null,
  "mensagem" text not null,
  "status" varchar not null default 'enviada',
  "erro_mensagem" text,
  "retries" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("tenant_id") references "clientes"("id") on delete cascade,
  foreign key("avaliacao_id") references "avaliacoes"("id") on delete set null,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "report_logs"(
  "id" varchar not null,
  "tenant_id" varchar not null,
  "periodo" varchar not null,
  "status" varchar not null default 'sent',
  "opened_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("tenant_id") references "clientes"("id") on delete cascade,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "bot_scripts"(
  "id" varchar not null,
  "tenant_id" varchar not null,
  "locale" varchar not null default 'pt',
  "messages" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("tenant_id") references "clientes"("id") on delete cascade,
  primary key("id")
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2026_01_01_000001_create_clientes_table',1);
INSERT INTO migrations VALUES(5,'2026_01_01_000002_create_avaliacoes_table',1);
INSERT INTO migrations VALUES(6,'2026_01_01_000003_create_transacoes_table',1);
INSERT INTO migrations VALUES(7,'2026_04_18_033814_architectural_hardening_multi_tenancy_and_indexes',2);
INSERT INTO migrations VALUES(8,'2026_04_18_033928_update_users_table_for_saas_roles_and_tenancy',3);
INSERT INTO migrations VALUES(9,'2026_04_18_034956_rename_cliente_id_to_tenant_id_on_users_table',4);
INSERT INTO migrations VALUES(10,'2026_04_18_040229_create_daily_metrics_summary_table',5);
INSERT INTO migrations VALUES(11,'2026_04_20_000001_add_google_maps_link_to_clientes_table',6);
INSERT INTO migrations VALUES(12,'2026_04_20_000002_add_subscription_fields_to_clientes',6);
INSERT INTO migrations VALUES(13,'2026_04_20_000003_create_audit_logs_table',6);
INSERT INTO migrations VALUES(14,'2026_04_20_000004_add_qr_customization_to_clientes',6);
INSERT INTO migrations VALUES(15,'2026_04_20_000005_create_notificacoes_logs_table',6);
INSERT INTO migrations VALUES(16,'2026_04_20_000006_create_report_logs_table',6);
INSERT INTO migrations VALUES(17,'2026_04_20_000007_create_bot_scripts_table',6);
INSERT INTO migrations VALUES(18,'2026_04_20_000008_standardize_tenant_id_in_avaliacoes',6);
INSERT INTO migrations VALUES(19,'2026_06_19_000001_reset_bot_scripts_to_official_flow',7);
INSERT INTO migrations VALUES(20,'2026_06_19_000002_force_official_bot_scripts_defaults',8);
INSERT INTO migrations VALUES(21,'2026_06_19_015318_add_branding_fields_to_clientes_table',9);
INSERT INTO migrations VALUES(22,'2026_06_19_022554_add_motivos_problema_to_clientes_table',10);
INSERT INTO migrations VALUES(23,'2026_06_19_100000_add_pack_idioma_to_clientes_and_transacoes',11);
INSERT INTO migrations VALUES(24,'2026_06_19_200000_split_feedback_sent_from_q_contact_in_bot_scripts',12);
