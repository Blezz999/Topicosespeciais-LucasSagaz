<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Projetos IFSC - MABEL e PTQA</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <!-- Banner Principal -->
    <header class="banner banner-small">
        <img src="img/abelhinha.jpg" alt="Abelha Jandaíra">
        <div class="caixa-info">
            <h1>Conheça Nossos Projetos</h1>
            <p>Explore os sistemas de monitoramento ambiental <strong>MABEL</strong> e <strong>PTQA</strong> desenvolvidos pelo IFSC Chapecó.</p>
        </div>
    </header>

    <!-- Seção de Projetos -->
    <main>
        <section class="atividades" id="detalhes-projetos">
            <h2>Detalhes e Foco de Monitoramento</h2>
            <div class="atividades-grid">

                <article class="atividade-item mabel full-width">
                    <h3><i class="fas fa-hive"></i> MABEL: Monitoramento de Abelhas</h3>
                    <p>
                        O sistema <strong>MABEL</strong> acompanha <strong>colmeias artificiais</strong> no câmpus, focando na <strong>saúde das abelhas sem ferrão</strong> por meio da coleta contínua de dados internos e externos.
                    </p>
                    <ul>
                        <li><strong>Variáveis Coletadas:</strong> Temperatura Interna, Temperatura Externa, Umidade Interna e Umidade Externa.</li>
                        <li><strong>Objetivo:</strong> Fornecer alertas e históricos para ajudar a equipe de pesquisa a identificar padrões de estresse térmico e otimizar a manutenção das colmeias.</li>
                    </ul>
                </article>

                <article class="atividade-item ptqa full-width">
                    <h3><i class="fas fa-wind"></i> PTQA: Protótipo de Qualidade do Ar</h3>
                    <p>
                        O <strong>PTQA</strong> monitora parâmetros ambientais ao redor do câmpus IFSC Chapecó, analisando a <strong>qualidade do ar</strong> para estudos acadêmicos sobre poluição e ventilação.
                    </p>
                    <ul>
                        <li><strong>Variáveis Coletadas:</strong> Temperatura, Umidade, Pressão Atmosférica, CO₂, Gases Voláteis (TVOC) e Índice de Qualidade do Ar (AQI).</li>
                        <li><strong>Objetivo:</strong> Permitir análise detalhada das condições ambientais e do conforto térmico, fornecendo dados para melhorias e pesquisas.</li>
                    </ul>
                </article>

            </div>
        </section>

        <!-- Botões de Navegação -->
        <nav class="nav-buttons" aria-label="Acesso rápido aos relatórios">
            <a href="relatorio_mabel.php" class="nav-btn mabel">
                <i class="fas fa-chart-line"></i> Ver Relatórios MABEL
            </a>
            <a href="relatorio_ptqa.php" class="nav-btn ptqa">
                <i class="fas fa-chart-bar"></i> Ver Relatórios PTQA
            </a>
            <a href="identificação.php" class="nav-btn">
                <i class="fas fa-id-card"></i> Ver Identificação
            </a>
        </nav>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
