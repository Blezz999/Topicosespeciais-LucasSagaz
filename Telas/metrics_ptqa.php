<?php
// =======================================================
// MÉTRICAS PTQA 
// =======================================================

// ---------- CONEXÃO ----------
require_once 'conexao.php'; // $conexao = PDO

// ---------- CAPTURA DE PARÂMETROS ----------
$data_inicial = $_GET['data_inicial'] ?? date('Y-m-01');
$data_final   = $_GET['data_final'] ?? date('Y-m-t');

// ==========================================================
// FUNÇÃO: Formatar data para BR
// ==========================================================
function formatarDataBR($data) {
    if (empty($data) || $data == '0000-00-00') return '-';
    return date('d/m/Y', strtotime($data));
}

// Datas completas (com hora)
$params = [
    ':data_inicial' => "$data_inicial 00:00:00",
    ':data_final'   => "$data_final 23:59:59",
];

// ---------- FUNÇÃO AUXILIAR ----------
function consultar(PDO $pdo, string $sql, array $params = [], bool $fetchAll = false) {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $fetchAll ? $stmt->fetchAll(PDO::FETCH_ASSOC) : $stmt->fetch(PDO::FETCH_ASSOC);
}

// ---------- CONSULTAS PRINCIPAIS ----------

// Temperatura média no período
$temp_media = consultar($conexao, "
    SELECT ROUND(AVG(temperatura),2) AS valor
    FROM leituraptqa
    WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
          BETWEEN :data_inicial AND :data_final
", $params)['valor'] ?? null;

// Umidade média por dia
$umid_dia_data = consultar($conexao, "
    SELECT dataleitura AS dia, ROUND(AVG(umidade),2) AS umid_media
    FROM leituraptqa
    WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
        BETWEEN :data_inicial AND :data_final
        AND umidade >= 0 AND umidade <= 100
    GROUP BY dataleitura
    ORDER BY dataleitura ASC
", $params, true);

// Máximo CO₂
$co2_max = consultar($conexao, "
    SELECT MAX(eco2) AS valor
    FROM leituraptqa
    WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
          BETWEEN :data_inicial AND :data_final
", $params)['valor'] ?? null;

// Pressão mínima por dia
$pressao_min_data = consultar($conexao, "
    SELECT dataleitura AS dia, MIN(pressao) AS pressao_min
    FROM leituraptqa
    WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
          BETWEEN :data_inicial AND :data_final
    GROUP BY dataleitura
    ORDER BY dataleitura ASC
", $params, true);

// Registros com AQI = 1
$aqi1_data = consultar($conexao, "
    SELECT dataleitura AS dia, horaleitura AS hora
    FROM leituraptqa
    WHERE aqi = 1
      AND STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
          BETWEEN :data_inicial AND :data_final
", $params, true);

// Estatísticas de temperatura
$temp_stats = consultar($conexao, "
    SELECT 
        ROUND(MAX(temperatura),2) AS temp_max,
        ROUND(MIN(temperatura),2) AS temp_min,
        ROUND(AVG(temperatura),2) AS temp_media
    FROM leituraptqa
    WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
          BETWEEN :data_inicial AND :data_final
", $params);

// TVOC médio por AQI
$tvoc_aqi_data = consultar($conexao, "
    SELECT aqi, ROUND(AVG(tvoc),2) AS tvoc_media
    FROM leituraptqa
    WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
          BETWEEN :data_inicial AND :data_final
    GROUP BY aqi
    ORDER BY aqi ASC
", $params, true);

// Top 5 dias com maior média de CO₂
$top5_co2_data = consultar($conexao, "
    SELECT dataleitura AS dia, ROUND(AVG(eco2),2) AS co2_media
    FROM leituraptqa
    WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
          BETWEEN :data_inicial AND :data_final
          AND eco2 > 0
          AND eco2 IS not NULL
    GROUP BY dataleitura
    ORDER BY co2_media DESC
", $params, true);

// ==========================================================
// FUNÇÃO: Formatar datas nos arrays para exibição
// ==========================================================
function formatarDatasArray($array, $campoData = 'dia') {
    foreach ($array as &$item) {
        if (isset($item[$campoData])) {
            $item[$campoData] = formatarDataBR($item[$campoData]);
        }
    }
    return $array;
}

// Aplicar formatação BR aos arrays de dados
$umid_dia_data_br = formatarDatasArray($umid_dia_data);
$pressao_min_data_br = formatarDatasArray($pressao_min_data);
$top5_co2_data_br = formatarDatasArray($top5_co2_data);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Métricas PTQA</title>
    <link rel="stylesheet" href="css/dashboard_ptqa.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- ===== Banner ===== -->
<div class="banner">
    <img src="/img/ptqa.jpg" alt="Abelha">
    <div class="caixa-info">
        <h2>Conheça nossos Projetos</h2>
        <p>Explore os sistemas de monitoramento ambiental MABEL e PTQA desenvolvidos pelo IFSC Chapecó.</p>
    </div>
</div>

<div class="header-banner">
    <h1>📊 Métricas PTQA</h1>
    <p>Período: <?= formatarDataBR($data_inicial) ?> a <?= formatarDataBR($data_final) ?></p>
</div>

<!-- ===== Botões ===== -->
<section class="btn-voltar-container">
    <button class="btn-voltar" onclick="window.location.href='dashboard_ptqa.php'">📊 Ir para Dashboard PTQA</button>
    <button class="btn-metrics" onclick="window.location.href='identificacao.php'">📖 Dicionário do Projeto</button>
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
        <button type="submit">Filtrar</button>
    </form>
</section>

<!-- ===== Cards de Métricas ===== -->
<section class="metrics-container">
    <div class="metric-card"><h3>🌡️ Temp Média</h3><p><?= $temp_media ?? '0.00' ?>°C</p></div>
    <div class="metric-card"><h3>🫁 Máx CO₂</h3><p><?= $co2_max ?? '0' ?> ppm</p></div>
    <div class="metric-card"><h3>🌬️ Total AQI=1</h3><p><?= count($aqi1_data) ?></p></div>
    <div class="metric-card"><h3>🌡️ Temp Máx/Mín/Média</h3>
        <p><?= $temp_stats['temp_max'] ?? '0.00' ?>° / <?= $temp_stats['temp_min'] ?? '0.00' ?>° / <?= $temp_stats['temp_media'] ?? '0.00' ?>°</p>
    </div>
</section>

<!-- ===== Gráficos ===== -->
<section class="charts-container">
    <div class="chart-card"><h3>💧 Umidade Média por Dia</h3><div id="chart-umid-dia"></div></div>
    <div class="chart-card"><h3>🌬️ Pressão Mínima por Dia</h3><div id="chart-pressao-min"></div></div>
    <div class="chart-card"><h3>⚗️ TVOC Médio por AQI</h3><div id="chart-tvoc-aqi"></div></div>
    <div class="chart-card"><h3>📈 Top 5 Dias Maior Média CO₂</h3><div id="chart-top5-co2"></div></div>
</section>

<script>
const amarelo = '#fbc02d';
const laranja = '#f57c00';

// Umidade diária
new ApexCharts(document.querySelector("#chart-umid-dia"), {
    chart:{ type:'line', height:300 },
    series:[{ name:'Umidade Média (%)', data:<?= json_encode(array_map(fn($r)=>(float)$r['umid_media'],$umid_dia_data)) ?> }],
    xaxis:{ 
        categories:<?= json_encode(array_column($umid_dia_data_br,'dia')) ?>,
        labels: { style: { colors: '#fff' } }
    },
    stroke:{ curve:'smooth' },
    colors:[amarelo],
    tooltip:{ theme:'dark' },
    theme: { mode: 'dark' }
}).render();

// Pressão mínima
new ApexCharts(document.querySelector("#chart-pressao-min"), {
    chart:{ type:'line', height:300 },
    series:[{ name:'Pressão Min (hPa)', data:<?= json_encode(array_map(fn($r)=>(float)$r['pressao_min'],$pressao_min_data)) ?> }],
    xaxis:{ 
        categories:<?= json_encode(array_column($pressao_min_data_br,'dia')) ?>,
        labels: { style: { colors: '#fff' } }
    },
    stroke:{ curve:'smooth' },
    colors:[laranja],
    tooltip:{ theme:'dark' },
    theme: { mode: 'dark' }
}).render();

// TVOC médio por AQI
new ApexCharts(document.querySelector("#chart-tvoc-aqi"), {
    chart:{ type:'bar', height:300 },
    series:[{ name:'TVOC Médio', data:<?= json_encode(array_map(fn($r)=>(float)$r['tvoc_media'],$tvoc_aqi_data)) ?> }],
    xaxis:{ 
        categories:<?= json_encode(array_column($tvoc_aqi_data,'aqi')) ?>,
        labels: { style: { colors: '#fff' } }
    },
    colors:[amarelo],
    tooltip:{ theme:'dark' },
    theme: { mode: 'dark' }
}).render();

// Top 5 CO₂
new ApexCharts(document.querySelector("#chart-top5-co2"), {
    chart:{ type:'bar', height:300 },
    series:[{ name:'CO₂ Média', data:<?= json_encode(array_map(fn($r)=>(float)$r['co2_media'],$top5_co2_data)) ?> }],
    xaxis:{ 
        categories:<?= json_encode(array_column($top5_co2_data_br,'dia')) ?>,
        labels: { style: { colors: '#fff' } }
    },
    colors:[laranja],
    tooltip:{ theme:'dark' },
    theme: { mode: 'dark' }
}).render();
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>