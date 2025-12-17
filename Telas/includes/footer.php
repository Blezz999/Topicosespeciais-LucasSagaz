<?php ?>
<!-- ====== ESTILO DO FOOTER ====== -->
<link rel="stylesheet" href="css/footer.css">

<!-- ====== FOOTER PRINCIPAL ====== -->
<footer class="site-footer">

  <div class="footer-content">

    <!-- =========================
         BLOCO DE LOCALIZAÇÃO
         Mostra onde fica o IFSC Chapecó
         ========================= -->
    <section class="footer-block location-block" aria-labelledby="localizacao-titulo">
      <h3 id="localizacao-titulo">Onde fica o IFSC?</h3>

      <address>
        <p>O IFSC Chapecó está localizado no bairro Seminário.</p>
        <p>Avenida Nereu Ramos, 3450 D — Chapecó, SC</p>
      </address>

      <!-- Links úteis -->
      <nav class="footer-links" aria-label="Links úteis do IFSC">
        <a href="https://www.ifsc.edu.br" target="_blank" rel="noopener noreferrer">
          Portal do IFSC
        </a>
        <a href="https://www.google.com/maps?q=ifsc+chapeco" target="_blank" rel="noopener noreferrer">
          Ver no Google Maps
        </a>
      </nav>

      <!-- Mapa integrado via iframe -->
      <div class="map-container">
        <iframe 
          src="https://maps.google.com/maps?q=ifsc%20chapeco&t=&z=15&ie=UTF8&iwloc=&output=embed"
          title="Localização do IFSC Chapecó no mapa"
          allowfullscreen
          loading="lazy">
        </iframe>
      </div>
    </section>

    <!-- =========================
         BLOCO DE CRÉDITOS E PARTICIPANTES
         Informações sobre professores, banco de dados e equipe
         ========================= -->
    <section class="footer-block credits-block" aria-labelledby="creditos-titulo">
      <h3 id="creditos-titulo">Créditos & Participantes</h3>
      
      <p>Módulo 8 — Aula de Tópicos Especiais</p>
      <p>Professor: Alexandre Anderson dos Santos</p>
      <p>Banco de dados disponibilizado por: Miguel de Borba</p>
      <p><a href="equipe.php">Ver todos os responsáveis pelo projeto</a></p>

      <!-- ====== DESENVOLVEDORES ====== -->
      <h4>Desenvolvedores</h4>
      <ul class="membros" aria-label="Desenvolvedores do projeto">
        <li class="membro">
          <img src="img/emilly.png" alt="Foto de Emilly Fortes" class="foto">
          <p><strong>Emilly Fortes</strong></p>
        </li>
        <li class="membro">
          <img src="img/lucas-s.png" alt="Foto de Lucas Sagaz" class="foto">
          <p><strong>Lucas Sagaz</strong></p>
        </li>
      </ul>

      <!-- ====== EQUIPE DE PESQUISA ====== -->
      <h4>Equipe de Pesquisa</h4>
      <ul class="membros" aria-label="Equipe de Pesquisa">
        <li class="membro">
          <img src="img/joao.png" alt="Foto de João Gabriel" class="foto">
          <p><strong>João Gabriel</strong></p>
        </li>
        <li class="membro">
          <img src="img/lucas-t.png" alt="Foto de Lucas Tartari" class="foto">
          <p><strong>Lucas Tartari</strong></p>
        </li>
        <li class="membro">
          <img src="img/julia.png" alt="Foto de Julia" class="foto">
          <p><strong>Julia</strong></p>
        </li>
      </ul>
    </section>

  </div>

  <!-- =========================
       RODAPÉ INFERIOR
       Direitos autorais e ano atual
       ========================= -->
  <div class="footer-bottom">
    <p>&copy; <?php echo date("Y"); ?> IFSC — Todos os direitos reservados.</p>
  </div>

</footer>
