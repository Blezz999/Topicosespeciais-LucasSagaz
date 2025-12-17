<?php
include("conexao.php");

// ------------------ PARÂMETROS ------------------
$data_inicial = $_GET['data_inicial'] ?? date('Y-m-01');
$data_final   = $_GET['data_final'] ?? date('Y-m-t');
$dia_especifico = $_GET['dia_especifico'] ?? '';

// ------------------ MONTAGEM DE CONSULTA ------------------
function criarSQL($colunas, $data_inicial, $data_final, $dia_especifico = '') {
    $datetimeField = "STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')";
    $where = $dia_especifico ? "dataInclusao = :dia" : "dataInclusao BETWEEN :data_ini AND :data_fim";

    $select = "$datetimeField AS datahora";
    foreach ($colunas as $col) $select .= ", $col";
    return ["sql" => "SELECT $select FROM leituramabel WHERE $where ORDER BY $datetimeField ASC", "where" => $where];
}

// ------------------ FUNÇÃO PARA EXECUTAR QUERY ------------------
function runQuery(PDO $con, $colunas, $data_inicial, $data_final, $dia_especifico = '') {
    $query = criarSQL($colunas, $data_inicial, $data_final, $dia_especifico);
    $stmt = $con->prepare($query["sql"]);

    if ($dia_especifico) {
        $stmt->bindValue(':dia', $dia_especifico);
    } else {
        $stmt->bindValue(':data_ini', $data_inicial);
        $stmt->bindValue(':data_fim', $data_final);
    }

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// ------------------ CONSULTAS ------------------
$rows_temp  = runQuery($conexao, ['ti', 'te'], $data_inicial, $data_final, $dia_especifico);
$rows_umid  = runQuery($conexao, ['hi', 'he'], $data_inicial, $data_final, $dia_especifico);
$rows_ninho = runQuery($conexao, ['ninho'], $data_inicial, $data_final, $dia_especifico);

// ------------------ CONVERSÃO PARA SÉRIES ------------------
function resultToSeries($rows, $cols) {
    $labels = [];
    $series = [];
    foreach ($cols as $c) $series[$c] = [];

    foreach ($rows as $row) {
        $labels[] = $row['datahora'];
        foreach ($cols as $c) {
            $series[$c][] = isset($row[$c]) ? (float)$row[$c] : null;
        }
    }

    return [$labels, $series];
}

list($labels_temp, $temp_series) = resultToSeries($rows_temp, ['ti', 'te']);
list($labels_umid, $umid_series) = resultToSeries($rows_umid, ['hi', 'he']);
list($labels_ninho, $ninho_series) = resultToSeries($rows_ninho, ['ninho']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard Projeto Mabel</title>
<link rel="stylesheet" href="css/dashboard_mabel.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Banner -->
<div class="banner">
    <img src="img/colmeia.jpg" alt="Abelha">
    <div class="caixa-info">
        <h2>Conheça nossos Projetos</h2>
        <p>Explore os sistemas de monitoramento ambiental MABEL e PTQA desenvolvidos pelo IFSC Chapecó.</p>
    </div>
</div>

<div class="header-banner">
    <h1>🐝 Dashboard Projeto Mabel</h1>
    <p>Monitoramento da colmeia artificial IFSC Chapecó</p>
    <p>Período: <?= htmlspecialchars($data_inicial) ?> a <?= htmlspecialchars($data_final) ?></p>
</div>

<section class="btn-metrics-container">
    <button class="btn-metrics" onclick="window.location.href='metrics_mabel.php'">📊 Métricas Resumidas</button>
    <button class="btn-metrics" onclick="window.location.href='identificacao.php'">❓ Dicionário do Projeto</button>
</section>

<section class="filtros-container">
    <form method="GET" class="filtros">
        <label>Data Inicial:
            <input type="date" name="data_inicial" value="<?= htmlspecialchars($data_inicial) ?>" required>
        </label>
        <label>Data Final:
            <input type="date" name="data_final" value="<?= htmlspecialchars($data_final) ?>" required>
        </label>
        <button type="submit">Filtrar</button>
    </form>
</section>

<section class="charts-container">
    <div class="chart-card"><h3>🌡️ Temperatura Interna e Externa</h3><div id="chart-temp"></div></div>
    <div class="chart-card"><h3>💧 Umidade Interna e Externa</h3><div id="chart-umid"></div></div>
    <div class="chart-card"><h3>🌡️ Temperatura do Ninho</h3><div id="chart-ninho"></div></div>
</section>

<script>
// Temperatura
new ApexCharts(document.querySelector("#chart-temp"), {
    chart: { type:'line', height:500, zoom:{enabled:true} },
    series: [
        { name:'TI (°C)', data: <?= json_encode($temp_series['ti']) ?> },
        { name:'TE (°C)', data: <?= json_encode($temp_series['te']) ?> }
    ],
    xaxis: { categories: <?= json_encode($labels_temp) ?>, type:'datetime' },
    yaxis: { title:{text:'Temperatura (°C)'} },
    stroke: { curve:'smooth' },
    tooltip: { theme:'dark', x:{format:'dd/MM/yyyy HH:mm:ss'} },
    colors: ['#fbc02d','#f57c00']
}).render();

// Umidade
new ApexCharts(document.querySelector("#chart-umid"), {
    chart: { type:'line', height:500, zoom:{enabled:true} },
    series: [
        { name:'HI (%)', data: <?= json_encode($umid_series['hi']) ?> },
        { name:'HE (%)', data: <?= json_encode($umid_series['he']) ?> }
    ],
    xaxis: { categories: <?= json_encode($labels_umid) ?>, type:'datetime' },
    yaxis: { title:{text:'Umidade (%)'} },
    stroke: { curve:'smooth' },
    tooltip: { theme:'dark', x:{format:'dd/MM/yyyy HH:mm:ss'} },
    colors: ['#2e7d32','#66bb6a']
}).render();

// Ninho
new ApexCharts(document.querySelector("#chart-ninho"), {
    chart: { type:'line', height:500, zoom:{enabled:true} },
    series: [{ name:'Ninho (°C)', data: <?= json_encode($ninho_series['ninho']) ?> }],
    xaxis: { categories: <?= json_encode($labels_ninho) ?>, type:'datetime' },
    yaxis: { title:{text:'Temperatura Ninho (°C)'} },
    stroke: { curve:'smooth' },
    tooltip: { theme:'dark', x:{format:'dd/MM/yyyy HH:mm:ss'} },
    colors: ['#ff7043']
}).render();
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
