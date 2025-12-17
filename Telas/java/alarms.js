// ================= alarms.js =================
document.addEventListener('DOMContentLoaded', () => {
    /**
     * Função responsável por validar formulários de datas
     * - Verifica se as duas datas foram preenchidas
     * - Garante que a data final não seja menor que a inicial
     * - Exibe alertas ao usuário conforme a situação
     */
    function validarForm(form) {
        form.addEventListener('submit', (event) => {
            const dataInicial = form.data_inicial.value;
            const dataFinal = form.data_final.value;

            // Se alguma data estiver em branco
            if (!dataInicial || !dataFinal) {
                alert("⚠️ Por favor, preencha ambas as datas.");
                event.preventDefault();
                return;
            }

            // Se a data final for menor que a inicial
            if (dataFinal < dataInicial) {
                alert("❌ A data final não pode ser anterior à data inicial.");
                event.preventDefault();
                return;
            }

            // Caso tudo esteja correto
            alert("⏳ Filtrando os dados, aguarde...");
        });
    }

    // Lista de formulários que vão usar essa validação
    const forms = [
        document.getElementById('form-filtros'),
        document.getElementById('form-filtros-ptqa'),
        document.getElementById('form-filtros-mabel')
    ];

    // Ativa a validação apenas nos formulários existentes
    forms.forEach(form => {
        if (form) validarForm(form);
    });
});
