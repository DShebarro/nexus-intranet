<?php
// Script de migração para adicionar categorias
// Executar: php database/migrate.php

$host = 'localhost';
$dbname = 'nexus_intranet';
$user = 'root';
$pass = 'ds051099@';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conectado ao banco de dados\n\n";
    
    // Verificar se a tabela categories já existe
    $check = $pdo->query("SHOW TABLES LIKE 'categories'");
    if ($check->rowCount() > 0) {
        echo "⚠️  Tabela 'categories' já existe. Verificando dados...\n";
    } else {
        // Criar tabela categories
        $pdo->exec("
            CREATE TABLE categories (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(100) NOT NULL,
                type ENUM('task', 'contract', 'site') NOT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_type (type)
            ) ENGINE=InnoDB
        ");
        echo "✅ Tabela 'categories' criada com sucesso.\n";
    }

    // Inserir categorias padrão somente se não existirem (evita duplicação)
    $defaultCategories = [
        'task'     => ['Desenvolvimento', 'Design', 'Infraestrutura', 'Documentação', 'Testes'],
        'contract' => ['Fornecedores', 'Parcerias', 'Serviços', 'Licenças', 'Manutenção'],
        'site'     => ['Sistemas Internos', 'Ferramentas', 'Documentação', 'Redes Sociais', 'Governamentais']
    ];
    
    $insertStmt = $pdo->prepare("
        INSERT IGNORE INTO categories (name, type)
        SELECT :name, :type FROM DUAL
        WHERE NOT EXISTS (
            SELECT 1 FROM categories WHERE name = :name AND type = :type
        )
    ");

    foreach ($defaultCategories as $type => $categories) {
        foreach ($categories as $category) {
            $insertStmt->execute(['name' => $category, 'type' => $type]);
            $rows = $insertStmt->rowCount();
            if ($rows > 0) {
                echo "  ✓ Categoria '{$category}' ({$type}) criada.\n";
            } else {
                echo "  ℹ️  Categoria '{$category}' ({$type}) já existe. Pulando.\n";
            }
        }
    }
    
    // Adicionar coluna category_id às tabelas existentes se necessário
    $tables = ['tasks', 'contracts', 'sites'];
    foreach ($tables as $table) {
        $check = $pdo->query("SHOW COLUMNS FROM {$table} LIKE 'category_id'");
        if ($check->rowCount() == 0) {
            $pdo->exec("ALTER TABLE {$table} ADD COLUMN category_id INT NULL, ADD FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL");
            echo "✅ Coluna 'category_id' adicionada à tabela '{$table}'\n";
        } else {
            echo "ℹ️  Coluna 'category_id' já existe na tabela '{$table}'\n";
        }
    }
    
    echo "\n🎉 Migração concluída com sucesso!\n";
    
} catch (PDOException $e) {
    echo "❌ Erro: " . $e->getMessage() . "\n";
    exit(1);
}
