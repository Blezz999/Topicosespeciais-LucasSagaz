<?php
// ==========================================================
// RELATÓRIO MABEL 
// ==========================================================
require_once 'conexao.php'; // $conexao deve ser PDO

// ==========================================================
// CAPTURA DE PARÂMETROS DE FILTRO
// ==========================================================
$data_inicial = $_GET['data_inicial'] ?? date('Y-m-01');
$data_final   = $_GET['data_final'] ?? date('Y-m-t');
$ordem_dia    = (($_GET['ordem_dia'] ?? 'asc') === 'desc') ? 'DESC' : 'ASC';

// ==========================================================
// FUNÇÃO: Formatar data para BR
// ==========================================================
function formatarDataBR($data) {
    if (empty($data) || $data == '0000-00-00') return '-';
    return date('d/m/Y', strtotime($data));
}

// Parâmetros padrão de data/hora para consultas
$params = [
    ':data_inicial' => "$data_inicial 00:00:00",
    ':data_final'   => "$data_final 23:59:59"
];

// ==========================================================
// FUNÇÕES AUXILIARES DE SQL
// ==========================================================

/**
 * Gera SQL para leitura geral de colunas com ordenação.
 */
function criarSQL(string $tabela, array $colunas, string $ordem = 'ASC'): string {
    $datetimeField = "STR_TO_DATE(CONCAT(dataInclusao,' ',horaInclusao),'%Y-%m-%d %H:%i:%s')";
    $select = "dataInclusao AS dia, horaInclusao AS hora";
    foreach ($colunas as $col) {
        $select .= ", $col";
    }

    return "SELECT $select
            FROM $tabela
            WHERE $datetimeField BETWEEN :data_inicial AND :data_final
            ORDER BY dataInclusao $ordem, horaInclusao $ordem";
}

/**
 * Gera SQL para Top 10 dias com maior ou menor média.
 */
function criarTop10(string $tabela, string $colunaValor, bool $desc = true): string {
    $ordem = $desc ? 'DESC' : 'ASC';
    return "SELECT DATE(dataInclusao) AS dia, 
                   ROUND(AVG(CAST($colunaValor AS DECIMAL(10,2))),2) AS valor_medio
            FROM $tabela
            WHERE dataInclusao BETWEEN :data_inicial AND :data_final
            GROUP BY dia
            ORDER BY valor_medio $ordem
            LIMIT 10";
}

/**
 * Executa uma consulta e retorna o objeto PDOStatement.
 */
function executarConsulta(PDO $pdo, string $sql, array $params = []): PDOStatement {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

// ==========================================================
// SQLs ESPECÍFICOS DO RELATÓRIO
// ==========================================================
$sql_ninho_diario = "
    SELECT DATE(dataInclusao) AS dia,
           ROUND(AVG(CAST(ninho AS DECIMAL(10,2))),2) AS temp_media
    FROM leituramabel
    WHERE dataInclusao BETWEEN :data_inicial AND :data_final
    GROUP BY dia
    ORDER BY dia $ordem_dia
";

// ==========================================================
// EXECUÇÃO DAS CONSULTAS
// ==========================================================
try {
    $result_ninho_diario = executarConsulta($conexao, $sql_ninho_diario, $params);
    $result_temp         = executarConsulta($conexao, criarSQL('leituramabel',['ti','te'],$ordem_dia), $params);
    $result_umid         = executarConsulta($conexao, criarSQL('leituramabel',['hi','he'],$ordem_dia), $params);
    $result_ninho        = executarConsulta($conexao, criarSQL('leituramabel',['ninho'],$ordem_dia), $params);
    $result_top_quente   = executarConsulta($conexao, criarTop10('leituramabel','ti', true), $params);
    $result_top_umido    = executarConsulta($conexao, criarTop10('leituramabel','he', true), $params);
} catch (PDOException $e) {
    die("Erro na consulta: " . $e->getMessage());
}

// ==========================================================
// FUNÇÃO: Formatar valor para exibição
// ==========================================================
function formatarValor($valor, $chave) {
    if ($valor === null || $valor === '') {
        return '-';
    }
    
    if ($chave === 'dia') {
        return formatarDataBR($valor);
    }
    
    if (is_numeric($valor)) {
        return number_format((float)$valor, 2, ',', '.');
    }
    
    return $valor;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Relatório Projeto Mabel</title>
<link rel="stylesheet" href="css/relatorio_mabel.css">
</head>
<body>

<?php 
// Inclui o header se existir
if (file_exists('includes/header.php')) {
    include 'includes/header.php'; 
}
?>

<!-- ======================================================
     CABEÇALHO
====================================================== -->
<div class="header-banner">
    <h1>🐝 Relatório Projeto Mabel</h1>
    <p>Monitoramento da colmeia artificial IFSC Chapecó</p>
</div>

<div class="voltar-container">
    <button class="btn-voltar" onclick="history.back();">← Voltar</button>
</div>

<!-- ======================================================
     FILTROS
====================================================== -->
<section class="filtros-container">
<form method="GET" class="filtros">
    <label>Data Inicial:
        <input type="date" name="data_inicial" value="<?= htmlspecialchars($data_inicial) ?>" required>
    </label>
    <label>Data Final:
        <input type="date" name="data_final" value="<?= htmlspecialchars($data_final) ?>" required>
    </label>
    <label>Ordenar por dia:
        <select name="ordem_dia">
            <option value="asc" <?= $ordem_dia=='ASC'?'selected':'' ?>>Cronológica (Menor → Maior)</option>
            <option value="desc" <?= $ordem_dia=='DESC'?'selected':'' ?>>Inversa (Maior → Menor)</option>
        </select>
    </label>
    <button type="submit">Filtrar</button>
</form>
</section>

<!-- ======================================================
     TABELAS DE RESULTADOS
====================================================== -->
<section class="cards-container">
<?php
$cards = [
    ["🌡️ Temperaturas Interna e Externa", $result_temp,  ['Data','Hora','Temp Interna','Temp Externa'], ['dia','hora','ti','te']],
    ["💧 Umidades Interna e Externa",     $result_umid,  ['Data','Hora','Umid. Interna','Umid. Externa'], ['dia','hora','hi','he']],
    ["🌡️ Temperatura do Ninho",          $result_ninho, ['Data','Hora','Temp Ninho'], ['dia','hora','ninho']],
    ["📊 Temp. Média do Ninho por Dia",  $result_ninho_diario, ['#','Data','Temp Média (°C)'], ['dia','temp_media']],
    ["🔥 Top 10 Dias Mais Quentes",      $result_top_quente, ['#','Data','Temp Interna Média'], ['dia','valor_medio']],
    ["💦 Top 10 Dias Mais Úmidos",       $result_top_umido,  ['#','Data','Umid. Externa Média'], ['dia','valor_medio']],
];

foreach ($cards as $card) {
    list($titulo, $result, $th, $keys) = $card;
    echo "<div class='card-relatorio card-fullwidth'>";
    echo "<h3>$titulo <span class='toggle-icon'>▶</span></h3>";
    echo "<table class='table-bg'><thead><tr>";
    foreach ($th as $t) echo "<th>$t</th>";
    echo "</tr></thead><tbody>";

    if ($result) {
        $contador = 1;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            if (in_array('#', $th)) {
                echo "<td>$contador</td>";
            }
            foreach ($keys as $k) {
                $valor = formatarValor($row[$k] ?? '', $k);
                echo "<td>" . htmlspecialchars($valor) . "</td>";
            }
            echo "</tr>";
            $contador++;
        }
        
        // Reset do ponteiro do resultado para possível reuso
        $result->execute($params);
    } else {
        echo "<tr><td colspan='" . count($th) . "' style='text-align: center;'>Nenhum dado encontrado</td></tr>";
    }

    echo "</tbody></table></div>";
}
?>
</section>

<!-- ======================================================
     SCRIPT DE EXPANDIR/COLAPSAR
====================================================== -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.card-relatorio h3').forEach(h => {
        h.addEventListener('click', () => {
            const card = h.parentElement;
            card.classList.toggle('open');
            
            // Atualiza o ícone
            const icon = h.querySelector('.toggle-icon');
            if (card.classList.contains('open')) {
                icon.textContent = '▼';
            } else {
                icon.textContent = '▶';
            }
        });
    });
});
</script>

<?php 
// Inclui o footer se existir
if (file_exists('includes/footer.php')) {
    include 'includes/footer.php'; 
}
?>
</body>
</html>