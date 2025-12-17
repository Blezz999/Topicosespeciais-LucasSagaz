-- Temperatura interna média por dia
SELECT 
    DATE(STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')) AS dia,
    AVG(ti) AS temperatura_interna_media
FROM leituramabel
WHERE STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')
      BETWEEN '2025-08-01 00:00:00' AND '2025-08-31 23:59:59'
GROUP BY dia
ORDER BY dia;

-- Temperatura externa média por dia
SELECT 
    DATE(STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')) AS dia,
    AVG(te) AS temperatura_externa_media
FROM leituramabel
WHERE STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')
      BETWEEN '2025-08-01 00:00:00' AND '2025-08-31 23:59:59'
GROUP BY dia
ORDER BY dia;

-- Umidade interna média por dia
SELECT 
    DATE(STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')) AS dia,
    AVG(hi) AS umidade_interna_media
FROM leituramabel
WHERE STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')
      BETWEEN '2025-08-01 00:00:00' AND '2025-08-31 23:59:59'
GROUP BY dia
ORDER BY dia;

-- Média do ninho por dia
SELECT 
    DATE(STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')) AS dia,
    AVG(ninho) AS media_ninho
FROM leituramabel
WHERE STR_TO_DATE(CONCAT(dataInclusao, ' ', horaInclusao), '%Y-%m-%d %H:%i:%s')
      BETWEEN '2025-08-01 00:00:00' AND '2025-08-31 23:59:59'
GROUP BY dia
ORDER BY dia;


-- Consulta mínima e máxima pressão por dia.
SELECT 
    DATE(STR_TO_DATE(CONCAT(dataleitura, ' ', horaleitura), '%Y-%m-%d %H:%i:%s')) AS dia,
    MIN(pressao) AS pressao_minima,
    MAX(pressao) AS pressao_maxima
FROM leituraptqa
WHERE STR_TO_DATE(CONCAT(dataleitura, ' ', horaleitura), '%Y-%m-%d %H:%i:%s')
      BETWEEN '2025-08-01 00:00:00' AND '2025-08-31 23:59:59'
GROUP BY dia
ORDER BY dia;

-- Quais índices apareceram mais vezes no período
SELECT 
    aqi,
    COUNT(*) AS quantidade,
    MIN(STR_TO_DATE(CONCAT(dataleitura, ' ', horaleitura), '%Y-%m-%d %H:%i:%s')) AS primeira_leitura,
    MAX(STR_TO_DATE(CONCAT(dataleitura, ' ', horaleitura), '%Y-%m-%d %H:%i:%s')) AS ultima_leitura
FROM leituraptqa
WHERE STR_TO_DATE(CONCAT(dataleitura, ' ', horaleitura), '%Y-%m-%d %H:%i:%s')
      BETWEEN '2025-08-01 00:00:00' AND '2025-08-31 23:59:59'
GROUP BY aqi
ORDER BY quantidade DESC;


-- Média de tvoc registrada
SELECT 
    DATE(STR_TO_DATE(CONCAT(dataleitura, ' ', horaleitura), '%Y-%m-%d %H:%i:%s')) AS dia,
    AVG(tvoc) AS media_tvoc
FROM leituraptqa
WHERE STR_TO_DATE(CONCAT(dataleitura, ' ', horaleitura), '%Y-%m-%d %H:%i:%s')
      BETWEEN '2025-08-01 00:00:00' AND '2025-08-31 23:59:59'
GROUP BY dia
ORDER BY dia;


