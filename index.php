<?php
// Configurações de erro e headers devem vir ANTES de qualquer HTML
ini_set("display_errors", 1);
error_reporting(E_ALL);
header('Content-Type: text/html; charset=utf-8'); // Sugiro UTF-8 em vez de ISO
?>
<html>
<head>
    <title>Exemplo PHP</title>
</head>
<body>

<?php
echo 'Versão Atual do PHP: ' . phpversion() . '<br>';

$servername = "192.168.1.50"; // Corrigido: ponto em vez de vírgula
$username = "root";
$password = "Senha123";
$database = "meubanco";

// Criar conexão
$link = new mysqli($servername, $username, $password, $database);

if ($link->connect_error) {
    die("Connect failed: " . $link->connect_error);
}

$valor_rand1 = rand(1, 999);
$valor_rand2 = strtoupper(substr(bin2hex(random_bytes(4)), 1));
$host_name = gethostname();
$host_ip = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1'; // IP do servidor que executa o script

// Usando Prepared Statements para segurança
$stmt = $link->prepare("INSERT INTO DADOS (id, data1, data2, hostname, ip) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("issss", $valor_rand1, $valor_rand2, $valor_rand2, $host_name, $host_ip);

if ($stmt->execute()) {
    echo "Novo registro criado com sucesso!";
} else {
    echo "Erro: " . $stmt->error;
}

$stmt->close();
$link->close();
?>
</body>
</html>