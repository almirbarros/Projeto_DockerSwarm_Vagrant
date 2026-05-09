<?php
	// Configurações de erro e headers devem vir ANTES de qualquer HTML
	ini_set("display_errors", 1);
	error_reporting(E_ALL);
	header('Content-Type: text/html; charset=utf-8'); // Sugiro UTF-8 em vez de ISO
?>

<html>
	<head>
		<title>Exemplo PHP - Cluster Swarm</title>
		<style>
			table { width: 100%; border-collapse: collapse; margin-top: 20px; }
			th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
			th { background-color: #f2f2f2; }
			.success { color: green; font-weight: bold; }
		</style>
	</head>
	<body>

		<?php
			echo '<strong>Versão Atual do PHP:</strong> ' . phpversion() . '<br>';

			$servername = "mysql-db";
			$username = "root";
			$password = "Senha123";
			$database = "meubanco";

			$link = new mysqli($servername, $username, $password, $database);

			if ($link->connect_error) {
				die("Falha na conexão: " . $link->connect_error . "<br>");
			}
			echo "Conectado com sucesso ao banco de dados!<br>";

			// Gerar dados aleatórios para teste
			$valor_rand1 = rand(1, 99999);
			$valor_rand2 = strtoupper(substr(bin2hex(random_bytes(4)), 1));
			$host_name = gethostname();
			$host_ip = $_SERVER['SERVER_ADDR'] ?? '127.0.0.1';

			// Inserir registro
			$stmt = $link->prepare("INSERT INTO DADOS (id, data1, data2, hostname, ip) VALUES (?, ?, ?, ?, ?)");
			$stmt->bind_param("issss", $valor_rand1, $valor_rand2, $valor_rand2, $host_name, $host_ip);

			if ($stmt->execute()) {
				echo "<span class='success'>Novo registro criado com sucesso por: $host_name ($host_ip)</span><br>";
			} else {
				echo "Erro ao inserir: " . $stmt->error . "<br>";
			}
			$stmt->close();

			// --- BLOCO PARA EXIBIR OS DADOS ---
			echo "<h2>Registros na Tabela DADOS</h2>";
			$sql = "SELECT id, data1, hostname, ip FROM DADOS ORDER BY id DESC LIMIT 10";
			$result = $link->query($sql);

			if ($result && $result->num_rows > 0) {
				echo "<table>";
				echo "<tr><th>ID Aleatório</th><th>Dado</th><th>Gerado por (Host)</th><th>IP do Container</th></tr>";
				while($row = $result->fetch_assoc()) {
					echo "<tr>";
					echo "<td>" . $row["id"] . "</td>";
					echo "<td>" . $row["data1"] . "</td>";
					echo "<td>" . $row["hostname"] . "</td>";
					echo "<td>" . $row["ip"] . "</td>";
					echo "</tr>";
				}
				echo "</table>";
			} else {
				echo "Nenhum dado encontrado ou erro na query: " . $link->error;
			}

			$link->close();
		?>
	</body>
</html>