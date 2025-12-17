<?php
// ================================================
// conexao.php — Conexão PDO exclusiva com MySQL
// ================================================

// --- CONFIGURAÇÕES DE ACESSO AO BANCO ---
$host    = "localhost";
$usuario = "root";
$senha   = "";
$banco   = "miguelde_oficina3";
$charset = "utf8mb4";

// --- STRING DE CONEXÃO (DSN) ---
$dsn = "mysql:host={$host};dbname={$banco};charset={$charset}";

try {
    // Cria conexão PDO
    $conexao = new PDO($dsn, $usuario, $senha, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,       // Mostra erros como exceções
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,  // Retorno padrão: array associativo
        PDO::ATTR_EMULATE_PREPARES => false,               // Usa prepared statements nativos
    ]);

} catch (PDOException $e) {
    // --- TRATAMENTO DE ERRO ---
    echo "<h2 style='color:red;'>❌ Erro ao conectar ao MySQL</h2>";
    echo "<p><strong>Mensagem:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Verifique:</p>";
    echo "<ul>
            <li>Se o servidor MySQL está ativo (ex: via XAMPP/WAMP).</li>
            <li>Usuário, senha e nome do banco: <code>{$banco}</code>.</li>
            <li>Se a extensão <code>pdo_mysql</code> está habilitada no php.ini.</li>
          </ul>";
    exit;
}

// --- RETORNO ---
return $conexao;
