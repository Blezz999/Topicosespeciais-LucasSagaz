<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tela Inicial - Projeto IFSC</title>

    <link rel="stylesheet" href="css/style.css">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <script src="java/alarms.js" defer></script>
</head>
<body>

    <?php include 'includes/header.php'; ?>

    <main>
        <section class="banner">
            <div class="banner-overlay">
                <img src="img/Abelha.jpg" alt="Abelha em flor" class="banner-img">
            </div>

            <div class="caixa-info">
                <h1>Monitoramento Ambiental Integrado</h1>
                <p>Sistema de acompanhamento das condições ambientais e das abelhas sem ferrão no IFSC Chapecó.</p>
            </div>
        </section>

        <section class="atividades container">
            <h2>🐝 Atividades e Componentes do Projeto</h2>

            <div class="atividades-grid"> 
                <article class="atividade-item card-geral"> 
                    <h3>Sobre o Projeto</h3>
                    <p>
                        O projeto integra monitoramento ambiental e dados das colmeias artificiais.  
                        <strong>Objetivo:</strong> acompanhar parâmetros críticos para a saúde das abelhas sem ferrão e para análise ambiental.  
                        <strong>Componentes:</strong> **MABEL** (Monitoramento de Abelhas) e **PTQA** (Protótipo de Qualidade do Ar).  
                    </p>
                </article>

                <article class="atividade-item card-mabel"> 
                    <h3><i class="fas fa-temperature-three-quarters"></i> Tabela LEITURAMABEL</h3>
                    <ul> 
                        <li>Armazena informações da colmeia artificial.</li>
                        <li>**Registro diário:** temperatura interna/externa e umidade interna/externa.</li>
                        <li>**Alertas:** top 10 dias mais quentes e úmidos.</li>
                    </ul>
                </article>

                <article class="atividade-item card-ptqa"> 
                    <h3><i class="fas fa-lungs"></i> Tabela LEITURAPTQA</h3>
                    <ul>
                        <li>Armazena informações da qualidade do ar no câmpus.</li>
                        <li>**Parâmetros:** temperatura, umidade, CO₂, pressão, AQI e TVOC.</li>
                        <li>**Finalidade:** avaliar conforto térmico, ventilação e poluentes químicos.</li>
                    </ul>
                </article>

                <article class="atividade-item card-mabel"> 
                    <h3>Sobre as Colmeias</h3>
                    <p>
                        Colmeias artificiais para abelhas sem ferrão mantêm temperatura e umidade ideais.  
                        **Função:** estudar comportamento das abelhas e monitorar o ambiente.  
                        **Benefícios:** controle do ambiente interno e observação do impacto climático externo.  
                    </p>
                </article>

                <article class="atividade-item card-geral full-width"> 
                    <h3><i class="fas fa-book-open"></i> História do Meliponário do IFSC</h3>
                    <p>
                        A iniciativa de construir um meliponário no campus Chapecó surgiu na disciplina de Oficina de Integração III, no módulo 5, pelo aluno Gabriel Riboli, que já era meliponicultor amador há alguns anos. O tema se enquadra perfeitamente na sustentabilidade. 
                    </p>
                    <a href="https://mabel.migueldebarba.com.br/historia.html" class="link-historia">
                        **Mais informações sobre a história. 🔗**
                    </a>
                </article>
            </div> 
        </section>
    
        <section class="nav-buttons container">
            <a href="relatorio_mabel.php" class="nav-btn btn-mabel">
                <i class="fas fa-hive"></i> Monitoramento MABEL
            </a>
            <a href="relatorio_ptqa.php" class="nav-btn btn-ptqa">
                <i class="fas fa-wind"></i> Monitoramento PTQA
            </a>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>