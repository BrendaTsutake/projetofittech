<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" href="img/logo-img.png" type="image/png">

    <style>

    @font-face { 
        font-family: "Louis George Cafe"; 
        src: url(fontes/louis_george_cafe/Louis\ George\ Cafe\ Light.ttf) format("truetype"); 
    }
    @font-face { 
        font-family: "mousse"; 
        src: url("fontes/mousse/Mousse-Regular.otf") format("otf"); 
    }
    body { 
        font-family: 'Louis George Cafe', Arial, sans-serif; 
        font-weight: 500; 
        font-size: 30px; 
        background-color: #FFF9EA; 
        margin: 0; 
        min-height: 100vh; 
        display: flex; 
        flex-direction: column; 
        justify-content: center; 
        align-items: center; 
    }
    .container { 
        display: grid; 
        grid-template-columns: 250px 1fr 300px; 
        gap: 20px; 
        max-width: 1400px; 
        margin: 0 auto;
        padding: 20px; 
    }
    .sidebar-left { 
        position: sticky; 
        top: 20px; 
        height: 95vh; 
    }
    .sidebar-left .logo img { 
        width: 150px; 
        margin-bottom: 30px; 
    }
    .sidebar-left ul { 
        list-style: none; 
        padding: 0; 
    }
    .sidebar-left ul li a { 
        display: flex; 
        align-items: center; 
        padding: 15px; 
        text-decoration: none; 
        color: #555; 
        font-size: 18px; 
        border-radius: 8px;
        margin-bottom: 10px; 
        font-weight: bold; 
    }
    .sidebar-left ul li a i { 
        margin-right: 15px; 
        width: 20px; 
    }
    .sidebar-left ul li a:hover, .sidebar-left ul li a.active { 
        background-color: #F8694D; 
        color: white; 
    }
    .sidebar-right { 
        position: sticky; 
        top: 20px; 
    }
    .user-tools { 
        background-color: #C8E6C9; 
        padding: 15px; 
        border-radius: 12px; 
        width: 400px; 
    }
    .search-bar { 
        display: flex; 
        align-items: center; 
        background-color: white; 
        padding: 8px; 
        border-radius: 20px; 
        margin-bottom: 20px; 
    }
    .search-bar input { 
        border: none; 
        outline: none; 
        background: none; 
        width: 100%; 
        margin-left: 8px; 
    }
    .tool-icons { 
        display: flex; 
        justify-content: space-around; 
    }
    .tool-icons a { 
        font-size: 22px; 
        color: #333; 
    }
    .tool-icons a.active { 
        color: #F8694D; 
    } 
    .feed { 
        max-width: 680px; 
    }
    .feed h2 { 
        font-family: 'Louis George Cafe', Arial, sans-serif; 
        margin-left: 250px; 
        font-weight: bolder; 
        color: #F8694D; 
        margin-bottom: 40px; 
        margin-top: 30px; 
        font-size: 39px; 
    }
    
    /* CSS do calendário */
    .calendar-container { 
        background-color: #ffffff; 
        border: 1px solid #eee; 
        border-radius: 12px; 
        padding: 25px; 
        width: 100%; 
        box-shadow: 0 4px 12px rgba(0,0,0,0.05); 
    }
    .calendar-header { 
        display: flex; 
        justify-content: space-between; 
        align-items: center; 
        margin-bottom: 20px; 
    }
    .calendar-header h3 { 
        font-family: 'Louis George Cafe', Arial, sans-serif; 
        font-size: 24px; 
        font-weight: bold; 
        color: #333; 
        margin: 0; 
    }
    .month-arrow { 
        background-color: #F1F1F1; 
        border: 1px solid #ddd;
        border-radius: 8px; 
        width: 40px; 
        height: 40px; 
        font-size: 16px; 
        color: #555; 
        cursor: pointer; 
        transition: background-color 0.2s; 
    }
    .month-arrow:hover { 
        background-color: #e7e7e7; 
    }
    .month-arrow:disabled { 
        background-color: #f9f9f9; 
        color: #ccc; 
        cursor: not-allowed; 
    }
    .calendar-grid { 
        width: 100%; 
        border-collapse: collapse; 
    }
    .calendar-grid th { 
        font-family: 'Louis George Cafe', Arial, sans-serif; 
        font-size: 14px; 
        font-weight: bold; 
        color: #777; 
        padding: 10px; 
        border: 1px solid #F0F0F0; 
        text-align: center; 
        background-color: #FAFAFA; 
    }
    
    /* Células do dia */
    .calendar-grid td { 
        font-family: 'Louis George Cafe', Arial, sans-serif; 
        font-size: 18px; font-weight: 500; 
        padding: 15px; border: 1px solid #F0F0F0; 
        text-align: center; height: 60px; color: #333; 
        cursor: pointer; 
        position: relative; /* Para a bolinha de nota */
    }
    .calendar-grid td:hover:not(.empty) {
        background-color: #fff9ea; /* Efeito hover suave */
    }

    .calendar-grid td.today { background-color: #FFECB3; border-color: #FFE082; font-weight: 900; color: #4a3e1a; }
    .calendar-grid td.empty { color: #bbb; background-color: #fdfdfd; cursor: default; }

    /* Indicador de nota (bolinha laranja) */
    .has-note::after {
        content: '';
        position: absolute;
        bottom: 8px;
        left: 50%;
        transform: translateX(-50%);
        width: 6px;
        height: 6px;
        background-color: #F8694D;
        border-radius: 50%;
    }
    </style>
</head>
<body>

    <div class="container">
        <nav class="sidebar-left">
            <div class="logo"><img src="img/logo.png" alt="FitTech Logo"></div>
            <ul>
                <li><a href="paginicial.php"><i class="fa-solid fa-house"></i> <span>Página Inicial</span></a></li>
                <li><a href="refeicao.php"><i class="fa-solid fa-utensils"></i> <span>Refeições</span></a></li>
                <li><a href="dicas.php"><i class="fa-solid fa-pencil"></i> <span>Dica do Nutri</span></a></li>
                <li><a href="progresso.php"><i class="fa-solid fa-chart-line"></i> <span>Progresso</span></a></li>
            </ul>
        </nav>

        <main class="feed">
            <h2>Calendário</h2>

            <div class="calendar-container">
                <div class="calendar-header">
                    <button id="prev-month" class="month-arrow"><i class="fa-solid fa-arrow-left"></i></button>
                    <h3 id="month-year"></h3>
                    <button id="next-month" class="month-arrow"><i class="fa-solid fa-arrow-right"></i></button>
                </div>

                <table class="calendar-grid">
                    <thead>
                        <tr><th>DOM</th><th>SEG</th><th>TER</th><th>QUA</th><th>QUI</th><th>SEX</th><th>SAB</th></tr>
                    </thead>
                    <tbody id="calendar-body"></tbody>
                </table>
            </div>
        </main>

        <aside class="sidebar-right">
            <div class="user-tools">
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Buscar...">
                </div>
                <div class="tool-icons">
                    <a href="calendario.php" title="Calendário" class="active"><i class="fa-solid fa-calendar-days"></i></a>
                    <a href="perfil.php" title="Perfil"><i class="fa-solid fa-user"></i></a>
                </div>
            </div>
        </aside>
    </div>

    <div class="modal fade" id="noteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Anotação - <span id="modalDateDisplay"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea id="noteText" class="form-control" rows="5" placeholder="Escreva sua anotação aqui..."></textarea>
                    <input type="hidden" id="noteDateHidden">
                </div>
                <div class="modal-footer d-flex justify-content-between">
                    <button type="button" class="btn btn-danger" id="deleteNoteBtn">Excluir</button>
                    
                    <div>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                        <button type="button" class="btn btn-primary" id="saveNoteBtn" style="background-color: #F8694D; border:none;">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        const monthYearEl = document.getElementById('month-year');
        const calendarBody = document.getElementById('calendar-body');
        const prevBtn = document.getElementById('prev-month');
        const nextBtn = document.getElementById('next-month');
        
        // Modal Elements
        const noteModal = new bootstrap.Modal(document.getElementById('noteModal'));
        const modalDateDisplay = document.getElementById('modalDateDisplay');
        const noteText = document.getElementById('noteText');
        const noteDateHidden = document.getElementById('noteDateHidden');
        const saveNoteBtn = document.getElementById('saveNoteBtn');
        const deleteNoteBtn = document.getElementById('deleteNoteBtn'); // Novo botão

        const meses = ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
        
        const ANO_CALENDARIO = 2025; 
        const hoje = new Date();
        
        let mesAtual = hoje.getMonth(); 
        if (hoje.getFullYear() !== ANO_CALENDARIO) { mesAtual = 0; } 

        let notasDoMes = {}; 

        // --- FUNÇÕES ---

        // 1. Busca as notas no banco
        async function carregarNotasEMontar(mes, ano) {
            try {
                const response = await fetch(`api_get_notes.php?mes=${mes + 1}&ano=${ano}`);
                notasDoMes = await response.json();
                gerarCalendario(mes, ano);
            } catch (error) {
                console.error("Erro ao carregar notas:", error);
                gerarCalendario(mes, ano); 
            }
        }

        function gerarCalendario(mes, ano) {
            calendarBody.innerHTML = '';
            monthYearEl.textContent = `${meses[mes]} de ${ano}`;
            
            let primeiroDia = new Date(ano, mes, 1).getDay();
            let diasNoMes = new Date(ano, mes + 1, 0).getDate();
            let data = 1;

            for (let i = 0; i < 6; i++) {
                let linha = document.createElement('tr');
                
                for (let j = 0; j < 7; j++) {
                    let celula = document.createElement('td');
                    
                    if (i === 0 && j < primeiroDia) {
                        celula.textContent = '-';
                        celula.classList.add('empty');
                    } else if (data > diasNoMes) {
                        celula.textContent = '-';
                        celula.classList.add('empty');
                    } else {
                        celula.textContent = data;
                        
                        let mesFormatado = (mes + 1).toString().padStart(2, '0');
                        let diaFormatado = data.toString().padStart(2, '0');
                        let dataFull = `${ano}-${mesFormatado}-${diaFormatado}`;

                        // Marca HOJE
                        if (data === hoje.getDate() && mes === hoje.getMonth() && ano === hoje.getFullYear()) {
                            celula.classList.add('today');
                        }

                        // Se tem nota, adiciona a classe e guarda o texto no dataset
                        if (notasDoMes[dataFull]) {
                            celula.classList.add('has-note');
                            celula.dataset.note = notasDoMes[dataFull];
                        }

                        // Evento de Clique para abrir Modal
                        celula.addEventListener('click', () => {
                            abrirModalNota(dataFull, notasDoMes[dataFull] || '');
                        });

                        data++;
                    }
                    linha.appendChild(celula);
                }
                calendarBody.appendChild(linha);
                if (data > diasNoMes) break;
            }
            atualizarBotoes();
        }

        function abrirModalNota(data, textoAtual) {
            let parts = data.split('-');
            modalDateDisplay.textContent = `${parts[2]}/${parts[1]}/${parts[0]}`;
            
            noteDateHidden.value = data;
            noteText.value = textoAtual;
            
            // Se não tem texto, podemos esconder o botão excluir se quiser, mas vamos deixar visível
            // deleteNoteBtn.style.display = textoAtual ? 'block' : 'none';
            
            noteModal.show();
        }

        // BOTÃO SALVAR
        saveNoteBtn.addEventListener('click', async () => {
            const data = noteDateHidden.value;
            const texto = noteText.value;

            if(!texto.trim()) {
                alert("A nota não pode estar vazia.");
                return;
            }

            try {
                const response = await fetch('api_save_note.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ data: data, texto: texto })
                });
                
                if (response.ok) {
                    noteModal.hide();
                    carregarNotasEMontar(mesAtual, ANO_CALENDARIO);
                } else {
                    alert('Erro ao salvar nota.');
                }
            } catch (error) {
                console.error('Erro:', error);
            }
        });

        // BOTÃO EXCLUIR (NOVO)
        deleteNoteBtn.addEventListener('click', async () => {
            const data = noteDateHidden.value;

            // Se o campo estiver vazio, nem precisa ir no banco, só fecha
            if (noteText.value.trim() === "") {
                 noteModal.hide();
                 return;
            }

            if(!confirm("Tem certeza que deseja excluir esta anotação?")) {
                return;
            }

            try {
                const response = await fetch('api_delete_note.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ data: data })
                });

                if (response.ok) {
                    noteModal.hide();
                    // Recarrega para tirar a bolinha laranja
                    carregarNotasEMontar(mesAtual, ANO_CALENDARIO);
                } else {
                    alert('Erro ao excluir nota.');
                }

            } catch (error) {
                console.error('Erro:', error);
            }
        });


        function atualizarBotoes() {
            prevBtn.disabled = (mesAtual === 0);
            nextBtn.disabled = (mesAtual === 11);
        }

        prevBtn.addEventListener('click', () => {
            if (mesAtual > 0) {
                mesAtual--;
                carregarNotasEMontar(mesAtual, ANO_CALENDARIO);
            }
        });
        
        nextBtn.addEventListener('click', () => {
            if (mesAtual < 11) {
                mesAtual++;
                carregarNotasEMontar(mesAtual, ANO_CALENDARIO);
            }
        });

        // Inicializa
        carregarNotasEMontar(mesAtual, ANO_CALENDARIO);
    });
</script>
</html>