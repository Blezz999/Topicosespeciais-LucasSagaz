<?php
include("conexao.php");

// ==========================
// Filtros de período e ordem
// ==========================
$data_inicial = $_GET['data_inicial'] ?? date('Y-m-01');
$data_final   = $_GET['data_final'] ?? date('Y-m-t');
$ordem_get    = strtolower($_GET['ordem'] ?? 'asc');
$ordem        = $ordem_get === 'desc' ? 'DESC' : 'ASC';

// ==========================
// Função auxiliar SQL base
// ==========================
function campoDataHora() {
    return "STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')";
}

// ==========================
// Criação da consulta SQL genérica (PDO seguro)
// ==========================
function criarSQL($col, $condicao, $ordem) {
    $where = $condicao ? "$condicao AND " . campoDataHora() . " BETWEEN :data_ini AND :data_fim"
                       : campoDataHora() . " BETWEEN :data_ini AND :data_fim";
    return "SELECT dataleitura AS dia, horaleitura AS hora, $col 
            FROM leituraptqa 
            WHERE $where 
            ORDER BY dataleitura $ordem, horaleitura $ordem";
}

// ==========================
// Execução segura de consultas com bindParam
// ==========================
function runQuery(PDO $con, $col, $condicao, $data_inicial, $data_final, $ordem) {
    $sql = criarSQL($col, $condicao, $ordem);
    $stmt = $con->prepare($sql);
    $stmt->bindValue(':data_ini', $data_inicial . ' 00:00:00');
    $stmt->bindValue(':data_fim', $data_final . ' 23:59:59');
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ==========================
// Executa consultas
// ==========================
$rows_temp    = runQuery($conexao, 'temperatura', '', $data_inicial, $data_final, 'ASC');
$rows_umid    = runQuery($conexao, 'umidade', 'umidade > 70', $data_inicial, $data_final, 'DESC');
$rows_co2     = runQuery($conexao, 'eco2', 'eco2 > 1000', $data_inicial, $data_final, $ordem);
$rows_pressao = runQuery($conexao, 'pressao', 'pressao < 1000', $data_inicial, $data_final, $ordem);
$rows_aqi     = runQuery($conexao, 'aqi', 'aqi >= 4', $data_inicial, $data_final, $ordem);
$rows_tvoc    = runQuery($conexao, 'tvoc', 'tvoc > 200', $data_inicial, $data_final, $ordem);

// ==========================
// Converte resultados em arrays (PDO)
// ==========================
function resultToArray($rows, $key) {
    $arr = [];
    foreach ($rows as $row) {
        $arr[] = [
            'label' => $row['dia'] . ' ' . $row['hora'],
            'value' => isset($row[$key]) ? (float)$row[$key] : null
        ];
    }
    return $arr;
}

$temp_data    = resultToArray($rows_temp, 'temperatura');
$umid_data    = resultToArray($rows_umid, 'umidade');
$co2_data     = resultToArray($rows_co2, 'eco2');
$pressao_data = resultToArray($rows_pressao, 'pressao');
$aqi_data     = resultToArray($rows_aqi, 'aqi');
$tvoc_data    = resultToArray($rows_tvoc, 'tvoc');
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard PTQA</title>
<link rel="stylesheet" href="css/dashboard_ptqa.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- ===== Banner ===== -->
<div class="banner">
    <img src="img/ptqa.jpg" alt="PTQA">
    <div class="caixa-info">
        <h2>Conheça nossos Projetos</h2>
        <p>Explore os sistemas de monitoramento ambiental MABEL e PTQA desenvolvidos pelo IFSC Chapecó.</p>
    </div>
</div>

<!-- ===== Header Interno ===== -->
<div class="header-banner">
    <h1>🌬️ Dashboard PTQA</h1>
    <p>Monitoramento da qualidade do ar no IFSC Chapecó</p>
    <p>Período: <?= htmlspecialchars($data_inicial) ?> a <?= htmlspecialchars($data_final) ?></p>
</div>

<!-- ===== Botões ===== -->
<section class="btn-metrics-container">
    <button class="btn-metrics" onclick="window.location.href='metrics_ptqa.php'">📊 Métricas Resumidas</button>
    <button class="btn-metrics" onclick="window.location.href='identificacao.php'">❓ Dicionário do projeto</button>
</section>

<!-- ===== Filtros ===== -->
<section class="filtros-container">
<form method="GET" class="filtros">
    <label>Data Inicial:
        <input type="date" name="data_inicial" value="<?= htmlspecialchars($data_inicial) ?>" required>
    </label>
    <label>Data Final:
        <input type="date" name="data_final" value="<?= htmlspecialchars($data_final) ?>" required>
    </label>
    <label>Ordem Cronológica:
        <select name="ordem">
            <option value="asc" <?= $ordem === 'ASC' ? 'selected' : '' ?>>Crescente</option>
            <option value="desc" <?= $ordem === 'DESC' ? 'selected' : '' ?>>Decrescente</option>
        </select>
    </label>
    <button type="submit">Filtrar</button>
</form>
</section>

<!-- ===== Gráficos ===== -->
<section class="charts-container">
    <div class="chart-card"><h3>🌡️ Temperatura</h3><div id="chart-temp"></div></div>
    <div class="chart-card"><h3>💧 Umidade > 70%</h3><div id="chart-umid"></div></div>
    <div class="chart-card"><h3>🫁 CO₂ > 1000 ppm</h3><div id="chart-co2"></div></div>
    <div class="chart-card"><h3>🌬️ Pressão < 1000 hPa</h3><div id="chart-pressao"></div></div>
    <div class="chart-card"><h3>📈 AQI ≥ 4</h3><div id="chart-aqi"></div></div>
    <div class="chart-card"><h3>☣️ TVOC > 200 ppb</h3><div id="chart-tvoc"></div></div>
</section>

<script>
function criarGraficoDark(id, titulo, data, cor){
    new ApexCharts(document.querySelector(id), {
        chart:{
            type:'line',
            height:400,
            zoom:{enabled:true},
            foreColor: '#f5f5f5',
        },
        series:[{name:titulo, data:data.map(d=>d.value)}],
        xaxis:{
            categories:data.map(d=>d.label),
            type:'datetime',
            labels:{ style:{ colors:'#f5f5f5' } }
        },
        yaxis:{
            labels:{ style:{ colors:'#f5f5f5' } },
            title:{ style:{ color:'#f5f5f5' } }
        },
        stroke:{curve:'smooth'},
        tooltip:{ theme:'dark', x:{format:'dd/MM/yyyy HH:mm:ss'} },
        grid:{ borderColor:'#444' },
        colors:[cor]
    }).render();
}

// Renderização dos gráficos
criarGraficoDark("#chart-temp","Temperatura (°C)",<?= json_encode($temp_data) ?>,"#f57c00");
criarGraficoDark("#chart-umid","Umidade (%)",<?= json_encode($umid_data) ?>,"#2e7d32");
criarGraficoDark("#chart-co2","CO₂ (ppm)",<?= json_encode($co2_data) ?>,"#d32f2f");
criarGraficoDark("#chart-pressao","Pressão (hPa)",<?= json_encode($pressao_data) ?>,"#1976d2");
criarGraficoDark("#chart-aqi","AQI",<?= json_encode($aqi_data) ?>,"#fbc02d");
criarGraficoDark("#chart-tvoc","TVOC (ppb)",<?= json_encode($tvoc_data) ?>,"#6a1b9a");
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
