-- ============================================================
-- Nexus Intranet — Schema MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS nexus_intranet
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE nexus_intranet;

-- Usuários do sistema
CREATE TABLE users (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    email       VARCHAR(150) UNIQUE NOT NULL,
    role        VARCHAR(80)         NOT NULL DEFAULT 'usuario',
    avatar      VARCHAR(2)          NOT NULL DEFAULT 'US',
    password    VARCHAR(255)        NOT NULL,
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tarefas Kanban
CREATE TABLE tasks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    title       VARCHAR(200)        NOT NULL,
    description TEXT,
    priority    ENUM('baixa','media','alta') NOT NULL DEFAULT 'media',
    status      ENUM('todo','progress','review','done') NOT NULL DEFAULT 'todo',
    due_date    DATE,
    created_by  INT,
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at  DATETIME            ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Contratos Corporativos
CREATE TABLE contracts (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    code        VARCHAR(50) UNIQUE  NOT NULL,
    partner     VARCHAR(150)        NOT NULL,
    object      VARCHAR(255)        NOT NULL,
    value       DECIMAL(12,2)       NOT NULL DEFAULT 0.00,
    status      ENUM('vigente','em_renovacao','vencido') NOT NULL DEFAULT 'vigente',
    end_date    DATE                NOT NULL,
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Sites / Links Rápidos
CREATE TABLE sites (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100)        NOT NULL,
    url         VARCHAR(255)        NOT NULL,
    description VARCHAR(255),
    is_internal TINYINT(1)          NOT NULL DEFAULT 1,
    status      ENUM('online','offline') NOT NULL DEFAULT 'online',
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Canais de Chat
CREATE TABLE chats (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    slug        VARCHAR(60) UNIQUE  NOT NULL,
    title       VARCHAR(100)        NOT NULL,
    type        ENUM('channel','direct','ai') NOT NULL DEFAULT 'channel',
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Mensagens de Chat
CREATE TABLE messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    chat_id     INT                 NOT NULL,
    sender_name VARCHAR(100)        NOT NULL,
    sender_type ENUM('user','ai','system') NOT NULL DEFAULT 'user',
    content     TEXT                NOT NULL,
    sent_at     DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chat_id) REFERENCES chats(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Registro de Atividades
CREATE TABLE activity_logs (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    type        VARCHAR(40)         NOT NULL DEFAULT 'info',
    description TEXT                NOT NULL,
    ip_address  VARCHAR(45),
    user_agent  VARCHAR(255),
    created_at  DATETIME            NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Inserir usuário padrão
INSERT INTO users (name, email, role, avatar, password) VALUES
('Carlos Silva', 'carlos@nexus.com', 'admin', 'CS', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- ============================================================
-- Categorias para organização
-- ============================================================

CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('task', 'contract', 'site') NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type (type)
) ENGINE=InnoDB;

-- Adicionar categoria_id às tabelas existentes
ALTER TABLE tasks ADD COLUMN category_id INT NULL,
    ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

ALTER TABLE contracts ADD COLUMN category_id INT NULL,
    ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

ALTER TABLE sites ADD COLUMN category_id INT NULL,
    ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

-- Inserir categorias padrão
INSERT INTO categories (name, type) VALUES
-- Tarefas
('Desenvolvimento', 'task'),
('Design', 'task'),
('Infraestrutura', 'task'),
('Documentação', 'task'),
('Testes', 'task'),
-- Contratos
('Fornecedores', 'contract'),
('Parcerias', 'contract'),
('Serviços', 'contract'),
('Licenças', 'contract'),
('Manutenção', 'contract'),
-- Sites
('Sistemas Internos', 'site'),
('Ferramentas', 'site'),
('Documentação', 'site'),
('Redes Sociais', 'site'),
('Governamentais', 'site');

-- ============================================================
-- Índices de performance
-- ============================================================
ALTER TABLE tasks ADD INDEX idx_status (status);
ALTER TABLE tasks ADD INDEX idx_category_status (category_id, status);
ALTER TABLE contracts ADD INDEX idx_status (status);
ALTER TABLE contracts ADD INDEX idx_end_date (end_date);
ALTER TABLE messages ADD INDEX idx_chat_sent (chat_id, sent_at);
ALTER TABLE activity_logs ADD INDEX idx_created_at (created_at);

-- ============================================================
-- Fase 2 — Soft deletes, audit trail, anexos, notificações
-- ============================================================
ALTER TABLE tasks ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL;
ALTER TABLE contracts ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL;
ALTER TABLE sites ADD COLUMN deleted_at DATETIME NULL DEFAULT NULL;

ALTER TABLE activity_logs
    ADD COLUMN user_id INT NULL,
    ADD COLUMN entity_type VARCHAR(50) NULL,
    ADD COLUMN entity_id INT NULL,
    ADD COLUMN action VARCHAR(40) NULL,
    ADD COLUMN old_values JSON NULL,
    ADD COLUMN new_values JSON NULL,
    ADD INDEX idx_entity (entity_type, entity_id);

CREATE TABLE IF NOT EXISTS attachments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    entity_type ENUM('task','contract','message') NOT NULL,
    entity_id INT NOT NULL,
    user_id INT NULL,
    original_name VARCHAR(255) NOT NULL,
    stored_name VARCHAR(255) NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    size_bytes INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_entity (entity_type, entity_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(40) NOT NULL DEFAULT 'info',
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    link VARCHAR(255) NULL,
    read_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_read (user_id, read_at),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS migrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL UNIQUE,
    executed_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;
