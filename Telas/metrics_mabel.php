<?php
// =====================================
// 🔌 Conexão com o banco de dados
// =====================================
require_once "conexao.php"; // PDO

// =====================================
//  Filtros de data (GET ou padrão = mês atual)
// =====================================
$data_inicial = $_GET['data_inicial'] ?? date('Y-m-01'); // primeiro dia do mês
$data_final   = $_GET['data_final'] ?? date('Y-m-t');    // último dia do mês

// ==========================================================
// FUNÇÃO: Formatar data para BR
// ==========================================================
function formatarDataBR($data) {
    if (empty($data) || $data == '0000-00-00') return '-';
    return date('d/m/Y', strtotime($data));
}

// =====================================
//  Consultas SQL — métricas e séries
// =====================================
$sql = [
    'temp_interna' => "SELECT ROUND(AVG(ti),2) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim",
    'temp_externa' => "SELECT ROUND(AVG(te),2) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim",
    'umid_interna' => "SELECT ROUND(AVG(hi),2) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim",
    'umid_externa' => "SELECT ROUND(AVG(he),2) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim",
    'ninho_max'    => "SELECT MAX(ninho) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim",
    'ninho_min'    => "SELECT MIN(ninho) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim",
    'dif_temp'     => "SELECT ROUND(AVG(ti - te),2) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim",
    'media_diaria_temp_interna' => "SELECT dataInclusao AS dia, ROUND(AVG(ti),2) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim GROUP BY dataInclusao ORDER BY dataInclusao",
    'media_diaria_umid_interna' => "SELECT dataInclusao AS dia, ROUND(AVG(hi),2) AS valor FROM leituramabel WHERE dataInclusao BETWEEN :inicio AND :fim GROUP BY dataInclusao ORDER BY dataInclusao"
];

// =====================================
//  Execução das consultas gerais (PDO)
// =====================================
$metrics = [];
$params = [':inicio' => $data_inicial, ':fim' => $data_final];

foreach (['temp_interna','temp_externa','umid_interna','umid_externa','ninho_max','ninho_min','dif_temp'] as $chave) {
    $stmt = $conexao->prepare($sql[$chave]);
    $stmt->execute($params);
    $metrics[$chave] = $stmt->fetchColumn() ?? '-';
}

// =====================================
//  Consultas para os gráficos
// =====================================
function buscarSerie($conexao, $query, $params) {
    $stmt = $conexao->prepare($query);
    $stmt->execute($params);
    $labels = $valores = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $labels[] = formatarDataBR($row['dia']); // Formata data para BR
        $valores[] = (float)$row['valor'];
    }
    return [$labels, $valores];
}

[$labels_temp, $values_temp] = buscarSerie($conexao, $sql['media_diaria_temp_interna'], $params);
[$labels_umid, $values_umid] = buscarSerie($conexao, $sql['media_diaria_umid_interna'], $params);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Métricas Projeto MABEL - 2º Grupo</title>
<link rel="stylesheet" href="css/dashboard_mabel.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
</head>
<body>

<?php include 'includes/header.php'; ?>

<!--  Banner principal -->
<div class="banner">
  <img src="img/colmeia.jpg" alt="Colmeia">
  <div class="caixa-info">
      <h2>Conheça nossos Projetos</h2>
      <p>Explore os sistemas de monitoramento ambiental MABEL e PTQA desenvolvidos pelo IFSC Chapecó.</p>
  </div>
</div>

<!--  Cabeçalho do dashboard -->
<div class="header-banner">
  <h1>🐝 Métricas Projeto MABEL</h1>
  <p>Monitoramento da colmeia artificial IFSC Chapecó</p>
  <p>Período: <?= formatarDataBR($data_inicial) ?> a <?= formatarDataBR($data_final) ?></p>
</div>

<!--  Navegação -->
<section class="btn-metrics-container">
  <button class="btn-metrics" onclick="window.location.href='dashboard_mabel.php'">📊 Dashboard Mabel</button>
  <button class="btn-metrics" onclick="window.location.href='identificacao.php'">📘 Dicionário do Projeto</button>
</section>

<!--  Filtros -->
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

<!--  Cards de métricas -->
<section class="metrics-container">
<?php 
$titulos = [
    'temp_interna'=>'🌡️ Temp. Interna Média',
    'temp_externa'=>'🌡️ Temp. Externa Média',
    'umid_interna'=>'💧 Umidade Interna Média',
    'umid_externa'=>'💧 Umidade Externa Média',
    'ninho_max'=>'🔥 Temperatura Ninho Máx.',
    'ninho_min'=>'❄️ Temperatura Ninho Mín.',
    'dif_temp'=>'📏 Diferença Média TI-TE'
];

// estrutura de repetição que preenche os cards das metricas
foreach ($metrics as $key => $valor): ?>
    <div class="metric-card">
        <h3><?= $titulos[$key] ?></h3>
        <p><?= htmlspecialchars($valor) ?> <?= strpos($key, 'umid') !== false ? '%' : '°C' ?></p>
    </div>
<?php endforeach; ?>
</section>

<!--  Gráficos -->
<section class="charts-container">
  <div class="chart-card">
      <h3>🌡️ Média Diária Temp. Interna</h3>
      <div id="apex-daily-temp"></div>
  </div>
  <div class="chart-card">
      <h3>💧 Média Diária Umidade Interna</h3>
      <div id="apex-daily-umid"></div>
  </div>
</section>

<script>
// Temperatura
new ApexCharts(document.querySelector("#apex-daily-temp"), {
  chart: { type: 'line', height: 450 },
  series: [{ name: 'Temp Interna', data: <?= json_encode($values_temp) ?> }],
  xaxis: { 
    categories: <?= json_encode($labels_temp) ?>,
    labels: { style: { colors: '#fff' } }
  },
  stroke: { curve: 'smooth' },
  colors: ['#fbc02d'],
  tooltip: { theme: 'dark' },
  theme: { mode: 'dark' }
}).render();

// Umidade
new ApexCharts(document.querySelector("#apex-daily-umid"), {
  chart: { type: 'line', height: 450 },
  series: [{ name: 'Umid Interna', data: <?= json_encode($values_umid) ?> }],
  xaxis: { 
    categories: <?= json_encode($labels_umid) ?>,
    labels: { style: { colors: '#fff' } }
  },
  stroke: { curve: 'smooth' },
  colors: ['#2e7d32'],
  tooltip: { theme: 'dark' },
  theme: { mode: 'dark' }
}).render();
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>