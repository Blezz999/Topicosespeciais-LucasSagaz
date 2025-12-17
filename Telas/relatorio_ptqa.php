<?php
// Este arquivo (conexao.php) DEVE definir $conexao como uma instância PDO
include("conexao.php"); 

// ============================
// Captura filtros de data e ordem
// ============================
$data_inicial = $_GET['data_inicial'] ?? date('Y-m-01');
$data_final   = $_GET['data_final'] ?? date('Y-m-t');
$ordem        = (strtoupper($_GET['ordem'] ?? 'asc') === 'DESC') ? 'DESC' : 'ASC'; 

// ============================
// Função: Formatar data para BR
// ============================
function formatarDataBR($data) {
    if (empty($data)) return '-';
    return date('d/m/Y', strtotime($data));
}

// ============================
// Função: Cria SQL Dinamicamente para Leituras Brutas
// ============================
function criarSQL($tabela, $colunas, $data_inicial, $data_final, $ordem='ASC') {
    $datetimeField = "STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')";
    $select = "dataleitura AS dia, horaleitura AS hora";
    foreach($colunas as $col) {
        $select .= ", $col";
    }
    return "SELECT $select
            FROM $tabela
            WHERE $datetimeField BETWEEN :data_inicial AND :data_final
            ORDER BY dataleitura $ordem, horaleitura $ordem";
}

// ============================
// Função: Executar consultas com PDO
// ============================
function executarConsulta($pdo, $sql, $params) {
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    } catch (PDOException $e) {
        die("Erro na consulta: " . $e->getMessage());
    }
}

// ============================
// Parâmetros para consultas de Leitura (com Hora)
// ============================
$params_leitura = [
    ':data_inicial' => "$data_inicial 00:00:00",
    ':data_final'   => "$data_final 23:59:59"
];

// ============================
// Consultas de Leituras Brutas (Tabela leituraptqa)
// ============================
$result_temp = executarConsulta($conexao, criarSQL('leituraptqa',['temperatura'],$data_inicial,$data_final,$ordem), $params_leitura);
$result_umid = executarConsulta($conexao, criarSQL('leituraptqa',['umidade'],$data_inicial,$data_final,$ordem), $params_leitura);
$result_co2  = executarConsulta($conexao, criarSQL('leituraptqa',['eco2'],$data_inicial,$data_final,$ordem), $params_leitura);
$result_tvoc = executarConsulta($conexao, criarSQL('leituraptqa',['tvoc'],$data_inicial,$data_final,$ordem), $params_leitura);

// ============================
// Consulta: Pressão mínima e máxima por dia
// ============================
$sql_pressao = "SELECT DATE(STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')) AS dia,
                         MIN(pressao) AS pressao_minima,
                         MAX(pressao) AS pressao_maxima
                 FROM leituraptqa
                 WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s')
                       BETWEEN :data_inicial AND :data_final
                 GROUP BY dia
                 ORDER BY dia $ordem";
$result_pressao = executarConsulta($conexao, $sql_pressao, $params_leitura);

// ============================
// 3. Consulta: Frequência do AQI
// ============================
$sql_aqi = "SELECT aqi, COUNT(*) AS quantidade,
                     ROUND(100*COUNT(*)/(SELECT COUNT(*) FROM leituraptqa 
                                         WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s') 
                                         BETWEEN :data_inicial_aqi AND :data_final_aqi),1) AS percentual
            FROM leituraptqa
            WHERE STR_TO_DATE(CONCAT(dataleitura,' ',horaleitura),'%Y-%m-%d %H:%i:%s') 
                  BETWEEN :data_inicial AND :data_final
            GROUP BY aqi
            ORDER BY quantidade DESC";

$params_aqi = array_merge($params_leitura, [
    ':data_inicial_aqi' => $params_leitura[':data_inicial'], 
    ':data_final_aqi'   => $params_leitura[':data_final']
]);

$stmt_aqi = $conexao->prepare($sql_aqi);
$stmt_aqi->execute($params_aqi);
$result_aqi = $stmt_aqi;

// ============================
// 4. Consultas: Médias Diárias (Tabela leituraptqa)
// ============================
function mediaDiaria($pdo, $campo, $ordem, $data_inicial, $data_final) {
    $sql = "SELECT dataleitura AS dia, ROUND(AVG($campo),2) AS media
            FROM leituraptqa
            WHERE dataleitura BETWEEN :data_inicial AND :data_final
            GROUP BY dataleitura ORDER BY dataleitura $ordem";
    $params = [':data_inicial' => $data_inicial, ':data_final' => $data_final];
    return executarConsulta($pdo, $sql, $params);
}

$medias_temp = mediaDiaria($conexao, 'temperatura', $ordem, $data_inicial, $data_final);
$medias_umid = mediaDiaria($conexao, 'umidade', $ordem, $data_inicial, $data_final);
$medias_co2  = mediaDiaria($conexao, 'eco2', $ordem, $data_inicial, $data_final);
$medias_tvoc = mediaDiaria($conexao, 'tvoc', $ordem, $data_inicial, $data_final);

// ============================
// Configuração dos Cards (Dados Brutos e Agregados)
// ============================
$cards_config = [
    "brutos" => [
        ["🌡️ Temperatura por Hora", $result_temp, ['Data','Hora','Temperatura (°C)'], ['dia','hora','temperatura']],
        ["💧 Umidade por Hora", $result_umid, ['Data','Hora','Umidade (%)'], ['dia','hora','umidade']],
        ["🫁 CO₂ por Hora", $result_co2, ['Data','Hora','CO₂ (ppm)'], ['dia','hora','eco2']],
        ["🌿 TVOC por Hora", $result_tvoc, ['Data','Hora','TVOC (ppb)'], ['dia','hora','tvoc']],
    ],
    "agregados" => [
        ["📊 Médias Diárias: Temperatura", $medias_temp, ['#','Data','Média (°C)'], ['dia','media']],
        ["💧 Médias Diárias: Umidade", $medias_umid, ['#','Data','Média (%)'], ['dia','media']],
        ["🫁 Médias Diárias: CO₂", $medias_co2, ['#','Data','Média (ppm)'], ['dia','media']],
        ["🌿 Médias Diárias: TVOC", $medias_tvoc, ['#','Data','Média (ppb)'], ['dia','media']],
        ["🌬️ Pressão (Min/Máx Diária)", $result_pressao, ['Data','Pressão Mín (hPa)','Pressão Máx (hPa)'], ['dia','pressao_minima','pressao_maxima']],
        ["📈 Frequência do Índice AQI", $result_aqi, ['AQI','Frequência','Percentual (%)'], ['aqi','quantidade','percentual']],
    ]
];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatório Projeto PTQA</title>
<link rel="stylesheet" href="css/relatorio_mabel.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<div class="header-banner">
    <h1>🌬️ Relatório Projeto PTQA</h1>
    <p>Monitoramento da qualidade do ar no IFSC Chapecó (<?= formatarDataBR($data_inicial) ?> a <?= formatarDataBR($data_final) ?>)</p>
</div>

<div class="voltar-container">
    <button class="btn-voltar" onclick="history.back();"><i class="fas fa-arrow-left"></i> Voltar</button>
</div>

<section class="filtros-container">
<form method="GET" class="filtros">
    <label>Data Inicial:
        <input type="date" name="data_inicial" value="<?= $data_inicial ?>" required>
    </label>
    <label>Data Final:
        <input type="date" name="data_final" value="<?= $data_final ?>" required>
    </label>
    <label>Ordem Cronológica:
        <select name="ordem">
            <option value="asc" <?= $ordem=='ASC'?'selected':'' ?>>Crescente</option>
            <option value="desc" <?= $ordem=='DESC'?'selected':'' ?>>Decrescente</option>
        </select>
    </label>
    <button type="submit"><i class="fas fa-filter"></i> Filtrar</button>
</form>
</section>

<section class="cards-container">
<?php
// ============================
// Loop para DADOS BRUTOS (Grid 2 colunas)
// ============================
echo "<div class='grid-brutos-container'>";
foreach ($cards_config['brutos'] as $c) {
    list($titulo, $result, $th, $keys) = $c;
    echo "<div class='card-relatorio card-halfwidth'>"; 
    echo "<h3>$titulo <span class='toggle-icon'>▶</span></h3>";
    echo "<table class='table-bg'><thead><tr>";
    foreach ($th as $t) echo "<th>$t</th>";
    echo "</tr></thead><tbody>";

    if ($result) {
        $contador = 1;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            if (in_array('#', $th)) echo "<td>$contador</td>";
            foreach ($keys as $k) {
                $val = isset($row[$k]) 
                    ? (is_numeric($row[$k]) 
                        ? (in_array($k, ['pressao_minima', 'pressao_maxima']) ? number_format($row[$k], 0) : number_format($row[$k], 2)) 
                        : ($k === 'dia' ? formatarDataBR($row[$k]) : $row[$k])
                      ) 
                    : '-';
                echo "<td>$val</td>";
            }
            echo "</tr>";
            $contador++;
        }
    }
    echo "</tbody></table></div>";
}
echo "</div>"; // Fecha grid-brutos-container

// ============================
// Loop para DADOS AGREGADOS E MÉDIAS (Largura Total)
// ============================
foreach ($cards_config['agregados'] as $c) {
    list($titulo, $result, $th, $keys) = $c;
    echo "<div class='card-relatorio card-fullwidth'>";
    echo "<h3>$titulo <span class='toggle-icon'>▶</span></h3>";
    echo "<table class='table-bg'><thead><tr>";
    foreach ($th as $t) echo "<th>$t</th>";
    echo "</tr></thead><tbody>";

    if ($result) {
        $contador = 1;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            if (in_array('#', $th)) echo "<td>$contador</td>"; 
            foreach ($keys as $k) {
                 $val = isset($row[$k]) 
                    ? (is_numeric($row[$k]) 
                        ? (in_array($k, ['pressao_minima', 'pressao_maxima']) ? number_format($row[$k], 0) : number_format($row[$k], 2)) 
                        : ($k === 'dia' ? formatarDataBR($row[$k]) : $row[$k])
                      ) 
                    : '-';
                echo "<td>$val</td>";
            }
            echo "</tr>";
            $contador++;
        }
    }
    echo "</tbody></table></div>";
}
?>
</section>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Adiciona evento de clique para abrir/fechar os cards (efeito accordion)
    document.querySelectorAll('.card-relatorio h3').forEach(h3 => {
        h3.addEventListener('click', () => h3.parentElement.classList.toggle('open'));
    });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html> 