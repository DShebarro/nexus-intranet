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