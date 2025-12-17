<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Descrição das Tabelas - MABEL e PTQA</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
    /* ===== Ajustes Visuais e Responsivos ===== */
    body {
      font-family: Arial, sans-serif;
      background-color: #121212;
      color: #f5f5f5;
      margin: 0;
    }

    .banner {
      position: relative;
      text-align: center;
      color: white;
    }

    .banner img {
      width: 100%;
      height: 300px;
      object-fit: cover;
      opacity: 0.7;
    }

    .caixa-info {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: rgba(0, 0, 0, 0.6);
      padding: 1.5rem 2rem;
      border-radius: 12px;
      text-align: center;
    }

    .caixa-info h1 {
      font-size: 1.8rem;
      margin-bottom: 0.5rem;
    }

    .nav-buttons {
      display: flex;
      justify-content: center;
      gap: 1rem;
      margin: 2rem 0;
      flex-wrap: wrap;
    }

    .nav-btn {
      background-color: #0077cc;
      color: white;
      border: none;
      padding: 0.75rem 1.5rem;
      font-size: 1rem;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s;
    }

    .nav-btn:hover {
      background-color: #005fa3;
      transform: translateY(-2px);
    }

    .relatorio {
      max-width: 900px;
      margin: 0 auto 4rem;
      background: #1e1e1e;
      padding: 2rem;
      border-radius: 12px;
      box-shadow: 0 0 15px rgba(0,0,0,0.3);
    }

    h2, h3 {
      text-align: center;
      color: #00bcd4;
      margin-top: 1.5rem;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
      background: #2b2b2b;
      border-radius: 10px;
      overflow: hidden;
    }

    th, td {
      padding: 0.8rem;
      text-align: left;
    }

    th {
      background: #0077cc;
      color: #fff;
    }

    tr:nth-child(even) {
      background-color: #1f1f1f;
    }

    tr:hover {
      background-color: #333;
    }

    @media (max-width: 768px) {
      .caixa-info h1 {
        font-size: 1.4rem;
      }
      .nav-btn {
        width: 100%;
      }
      table, th, td {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>

  <!-- Cabeçalho -->
  <?php include 'includes/header.php'; ?>

  <!-- ====== BANNER ====== -->
  <div class="banner">
    <img src="img/colmeia.jpg" alt="Abelha representando o projeto MABEL e PTQA">
    <table>
      <h1>📊 Tabelas MABEL & PTQA</h1>
      <p>Informações detalhadas de cada campo dos sensores e medições coletadas.</p>
  </table>
  </div>

  <!-- Botões de navegação -->
  <section class="nav-buttons">
    <button class="nav-btn" onclick="window.location.href='dashboard_mabel.php'">
      🧊 Ir para Dashboard MABEL
    </button>
    <button class="nav-btn" onclick="window.location.href='dashboard_ptqa.php'">
      🌫️ Ir para Dashboard PTQA
    </button>
  </section>
  <!-- ====== RELATÓRIOS ====== -->
  <section class="relatorio">
    <h2>Descrição das Tabelas</h2>

    <!-- Tabela MABEL -->
    <h3>🧊 Tabela: leituramabel</h3>
    <table>
      <tr><th>Campo</th><th>Descrição</th></tr>
      <tr><td title="Identificador único da leitura">idleituramabel</td><td>ID da leitura (chave primária)</td></tr>
      <tr><td title="Endereço MAC do dispositivo MABEL">macmabel_idmacmabel</td><td>Identificação do sensor MABEL</td></tr>
      <tr><td title="Data da leitura (AAAA-MM-DD)">dataInclusao</td><td>Data em que a leitura foi registrada</td></tr>
      <tr><td title="Hora da leitura (HH:MM:SS)">horaInclusao</td><td>Horário em que a leitura foi registrada</td></tr>
      <tr><td title="Umidade interna (%)">hi</td><td>Umidade interna medida pelo sensor</td></tr>
      <tr><td title="Umidade externa (%)">he</td><td>Umidade externa medida pelo sensor</td></tr>
      <tr><td title="Temperatura interna (°C)">ti</td><td>Temperatura dentro do ambiente</td></tr>
      <tr><td title="Temperatura externa (°C)">te</td><td>Temperatura fora do ambiente</td></tr>
      <tr><td title="Sensor auxiliar">ninho</td><td>Leitura adicional (ex: setor interno)</td></tr>
      <tr><td title="Data e hora combinadas">datahora</td><td>Data e hora completas da leitura</td></tr>
    </table>

    <!-- Tabela PTQA -->
    <h3>🌫️ Tabela: leituraptqa</h3>
    <table>
      <tr><th>Campo</th><th>Descrição</th></tr>
      <tr><td title="Identificador único da leitura">idleituraptqa</td><td>ID da leitura (chave primária)</td></tr>
      <tr><td title="Endereço MAC do dispositivo PTQA">macptqa_idmacptqa</td><td>Identificação do sensor PTQA</td></tr>
      <tr><td title="Data da leitura">dataleitura</td><td>Data em que os dados foram coletados</td></tr>
      <tr><td title="Hora da leitura">horaleitura</td><td>Horário exato da coleta</td></tr>
      <tr><td title="Pressão atmosférica (hPa)">pressao</td><td>Pressão do ar no ambiente</td></tr>
      <tr><td title="Temperatura do ar (°C)">temperatura</td><td>Temperatura medida no local</td></tr>
      <tr><td title="Concentração de CO₂ (ppm)">eco2</td><td>Indica ventilação e qualidade do ar — valores altos indicam ar abafado</td></tr>
      <tr><td title="Compostos Orgânicos Voláteis (ppb)">tvoc</td><td>Detecta poluentes químicos no ar interno</td></tr>
      <tr><td title="Umidade relativa do ar (%)">umidade</td><td>Percentual de umidade ambiente</td></tr>
      <tr><td title="Índice de Qualidade do Ar (0–500)">aqi</td><td>Classifica a qualidade do ar (quanto menor, melhor)</td></tr>
    </table>
  </section>

  <!-- Rodapé -->
  <?php include 'includes/footer.php'; ?>

</body>
</html>
