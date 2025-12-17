<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sobre os Projetos - IFSC</title>

  <!-- Estilo principal -->
  <link rel="stylesheet" href="css/style.css">
  
  <!-- Font Awesome para ícones -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

  <!-- Cabeçalho do site -->
  <?php include 'includes/header.php'; ?>

  <!-- ====== BANNER ====== -->
   <section class="banner">
    <img src="img/abelhinha.jpg" alt="Abelha">
    
    <!-- Caixa de informação no banner -->
    <div class="caixa-info">
      <h2>Conheça nossos Projetos</h2>
      <p>Explore os sistemas de monitoramento ambiental <strong>MABEL</strong> e <strong>PTQA</strong> desenvolvidos pelo IFSC Chapecó.</p>
    </div>
  </div>

  <!-- ====== BOTÕES DE NAVEGAÇÃO ====== -->
  <div class="nav-buttons">
    <!-- Botão MABEL: tons verde IFSC (colmeia) -->
    <a href="relatorio_mabel.php" class="nav-btn mabel">
      <i class="fas fa-chart-line"></i> Mabel
    </a>
    
    <!-- Botão PTQA: tons azul/mato -->
    <a href="relatorio_ptqa.php" class="nav-btn ptqa">
      <i class="fas fa-cogs"></i> PTQA
    </a>
  </div>

  <!-- ====== SEÇÃO EXPLICATIVA ====== -->
  <section class="atividades" style="max-width: 900px; margin: 40px auto;">
    <h2>Sobre os Projetos</h2>
    
    <!-- Descrição MABEL -->
    <p>
      <strong>MABEL</strong> (Monitoramento de Abelhas) realiza acompanhamento de colmeias artificiais, coletando dados de temperatura, umidade e alertas para garantir a saúde das abelhas sem ferrão.  
      <!-- Observação: cores do CSS da seção MABEL devem remeter a colmeia e abelhas (amarelo/laranja) -->
    </p>
    
    <!-- Descrição PTQA -->
    <p>
      <strong>PTQA</strong> (Protótipo de Qualidade do Ar) mede parâmetros ambientais como temperatura, umidade, CO₂ e índices de poluição, permitindo análise detalhada da qualidade do ar no câmpus IFSC Chapecó.  
      <!-- Observação: cores do CSS da seção PTQA devem remeter a água/mato (azul/verde) -->
    </p>
  </section>

  <!-- Rodapé do site -->
  <?php include 'includes/footer.php'; ?>

</body>
</html>
