
<?php
include 'conexao.php';
// Quem fez: Lucas Sagaz.

// Pegando filtros de data (enviados via formulário ou GET)
$dataInicial = $_GET['data_inicial'] ?? '2025-01-01';
$dataFinal   = $_GET['data_final']   ?? '2025-12-31';

/* ================================
   CONSULTAS PROJETO MABEL
================================ */

// 1) Exibir data e hora
$sql = "SELECT datahora FROM LEITURAMABEL 
        WHERE datahora BETWEEN :data_inicial AND :data_final";
$stmt = $pdo->prepare($sql);
$stmt->execute([':data_inicial'=>$dataInicial, ':data_final'=>$dataFinal]);
$resultDataHora = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 2) Temperatura interna média no período
$sql = "SELECT AVG(ti) AS media_ti 
        FROM LEITURAMABEL       
        WHERE datahora BETWEEN :data_inicial AND :data_final";
$stmt = $pdo->prepare($sql);
$stmt->execute([':data_inicial'=>$dataInicial, ':data_final'=>$dataFinal]);
$mediaTI = $stmt->fetch(PDO::FETCH_ASSOC);

// 3) Diferença média TI - TE
$sql = "SELECT AVG(ti - te) AS dif_media 
        FROM LEITURAMABEL 
        WHERE datahora BETWEEN :data_inicial AND :data_final";
$stmt = $pdo->prepare($sql);
$stmt->execute([':data_inicial'=>$dataInicial, ':data_final'=>$dataFinal]);
$difMedia = $stmt->fetch(PDO::FETCH_ASSOC);


/* ================================
   CONSULTAS PROJETO PTQA
================================ */

// 1) Temperatura média no período
$sql = "SELECT AVG(temperatura) AS temp_media 
        FROM LEITURAPTQA 
        WHERE datahora BETWEEN :data_inicial AND :data_final";
$stmt = $pdo->prepare($sql);
$stmt->execute([':data_inicial'=>$dataInicial, ':data_final'=>$dataFinal]);
$tempMedia = $stmt->fetch(PDO::FETCH_ASSOC);

// 2) Registros com AQI ≥ 4
$sql = "SELECT * FROM LEITURAPTQA 
        WHERE aqi >= 4 
        AND datahora BETWEEN :data_inicial AND :data_final";
$stmt = $pdo->prepare($sql);
$stmt->execute([':data_inicial'=>$dataInicial, ':data_final'=>$dataFinal]);
$aqiRuim = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
