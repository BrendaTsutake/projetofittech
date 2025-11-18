<?php
// Inicia a sessão
session_start();

// Verifica se o usuário NÃO está logado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// Lógica para pegar os 7 dias da semana atual
$diasDaSemana = ['DOM', 'SEG', 'TER', 'QUA', 'QUI', 'SEX', 'SÁB'];
$hoje = new DateTime();
$hojeIndex = (int)$hoje->format('w'); 
$dataHojeStr = $hoje->format('Y-m-d');

$semana = [];
for ($i = 0; $i < 7; $i++) {
    $data = new DateTime();
    $data->modify(($i - $hojeIndex) . ' days');
    $semana[] = [
        'nome' => $diasDaSemana[$i],
        'letra' => substr($diasDaSemana[$i], 0, 1),
        'data' => $data->format('Y-m-d'),
        'ativo' => ($data->format('Y-m-d') == $dataHojeStr)
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Refeição</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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
        font-size: 22px;
        background-color: #FFF9EA;
        margin: 0;
        min-height: 100vh;
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
        margin-bottom: 20px; /* Espaço para o total de Kcal */
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
    .feed-content {
        padding: 10px;
    }
    .feed-header {
        text-align: center;
        margin-bottom: 30px;
        font-weight: bold;
        color: #555;
        font-size: 40px;
    }
    .week-navigator {
        display: flex;
        justify-content: space-around;
        align-items: center;
        margin-bottom: 40px;
    }
    .day {
        display: flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
    }
    .day-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        border: 2px solid #ccc;
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 8px;
        transition: all 0.2s;
        font-weight: bold;
    }
    .day span {
        font-size: 18px;
        text-transform: uppercase;
        font-weight: bold;
        color: #888;
    }
    .day.active .day-circle {
        border-color: #F8694D;
        background-color: #F8694D;
        color: white;
    }
    .day.active span {
        color: #F8694D;
    }
    
    .meal-item {
        background-color: #E8F5E9;
        border-radius: 15px;
        margin-bottom: 15px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .meal-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .meal-header:hover {
        transform: translateY(-3px);
    }
    .meal-info {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .meal-info i {
        font-size: 26px;
        color: #555;
    }
    .meal-info .meal-name {
        font-size: 20px;
        font-weight: bold;
        color: #333;
    }
    .meal-header > i.fa-plus {
        font-size: 22px;
        color: #555;
        cursor: pointer;
    }
    .meal-content {
        padding: 0 20px 20px 20px;
        display: none; 
    }
    .meal-content ul {
        list-style: none;
        padding: 0;
        margin: 0 0 15px 0;
    }
    .meal-content li {
        display: flex;
        justify-content: space-between;
        padding: 8px 0;
        border-bottom: 1px solid #d4e9d5;
        font-size: 18px;
    }
    .meal-content li span:first-child {
        font-weight: bold;
        color: #333;
    }
    .meal-content li span:last-child {
        color: #F8694D;
        font-weight: bold;
    }
    .meal-content li .food-qty {
        color: #555;
        font-size: 16px;
        margin-left: 10px;
    }
    .meal-content li i {
        cursor: pointer;
        color: #888;
        margin-left: 10px;
    }
    .meal-content li i:hover {
        color: #F8694D;
    }
    .meal-footer {
        display: flex;
        justify-content: space-between;
        font-size: 18px;
        font-weight: bold;
        color: #333;
    }
    .total-kcal-card {
        background-color: #F8694D;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
    }
    .total-kcal-card h3 {
        font-family: 'mousse', Arial, sans-serif;
        color: #FFF9EA;
        font-size: 30px;
        margin: 0;
    }
    .total-kcal-card p {
        font-size: 50px;
        font-weight: bold;
        color: #FFF9EA;
        margin: 0;
    }
    </style>
</head>
<body>

    <div class="container">
        <nav class="sidebar-left">
            <div class="logo">
                <img src="img/logo.png" alt="FitTech Logo">
            </div>
            <ul>
                <li><a href="paginicial.php"><i class="fa-solid fa-house"></i> <span>Página Inicial</span></a></li>
                <li><a href="refeicao.php" class="active"><i class="fa-solid fa-utensils"></i> <span>Refeições</span></a></li>
                <li><a href="dicas.php"><i class="fa-solid fa-pencil"></i> <span>Dica do Nutri</span></a></li>
                <li><a href="progresso.php"><i class="fa-solid fa-chart-line"></i> <span>Progresso</span></a></li>
            </ul>
        </nav>

        <main class="feed-content">
            <div class="feed-header" id="feed-title">Hoje</div>
    
            <div class="week-navigator">
                <?php foreach ($semana as $dia): ?>
                    <div class="day <?php echo $dia['ativo'] ? 'active' : ''; ?>" data-date="<?php echo $dia['data']; ?>" data-day-name="<?php echo $dia['nome']; ?>">
                        <div class="day-circle"><?php echo $dia['letra']; ?></div>
                        <span><?php echo $dia['nome']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="meals-list">
                <div class="meal-item" data-meal-type="Café da Manhã">
                    <div class="meal-header">
                        <div class="meal-info">
                            <i class="fa-solid fa-sun"></i>
                            <span class="meal-name">Café da Manhã</span>
                        </div>
                        <i class="fa-solid fa-plus add-food-btn"></i>
                    </div>
                    <div class="meal-content">
                        <ul></ul>
                        <div class="meal-footer">
                            <span>Total Refeição</span>
                            <span class="meal-total-kcal">0 kcal</span>
                        </div>
                    </div>
                </div>

                <div class="meal-item" data-meal-type="Almoço">
                    <div class="meal-header">
                        <div class="meal-info">
                            <i class="fa-solid fa-cloud-sun"></i>
                            <span class="meal-name">Almoço</span>
                        </div>
                        <i class="fa-solid fa-plus add-food-btn"></i>
                    </div>
                    <div class="meal-content">
                        <ul></ul>
                        <div class="meal-footer">
                            <span>Total Refeição</span>
                            <span class="meal-total-kcal">0 kcal</span>
                        </div>
                    </div>
                </div>
                
                <div class="meal-item" data-meal-type="Jantar">
                    <div class="meal-header">
                        <div class="meal-info">
                            <i class="fa-solid fa-moon"></i>
                            <span class="meal-name">Jantar</span>
                        </div>
                        <i class="fa-solid fa-plus add-food-btn"></i>
                    </div>
                    <div class="meal-content">
                        <ul></ul>
                        <div class="meal-footer">
                            <span>Total Refeição</span>
                            <span class="meal-total-kcal">0 kcal</span>
                        </div>
                    </div>
                </div>

                <div class="meal-item" data-meal-type="Snacks/Outros">
                    <div class="meal-header">
                        <div class="meal-info">
                            <i class="fa-solid fa-cookie-bite"></i>
                            <span class="meal-name">Snacks/Outros</span>
                        </div>
                        <i class="fa-solid fa-plus add-food-btn"></i>
                    </div>
                    <div class="meal-content">
                        <ul></ul>
                        <div class="meal-footer">
                            <span>Total Refeição</span>
                            <span class="meal-total-kcal">0 kcal</span>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <aside class="sidebar-right">
            <div class="user-tools">
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Buscar...">
                </div>
                <div class="tool-icons">
                    <a href="calendario.php" title="Calendário"><i class="fa-solid fa-calendar-days"></i></a>
                    <a href="perfil.php" title="Perfil"><i class="fa-solid fa-user"></i></a>
                </div>
            </div>
            
            <div class="total-kcal-card">
                <h3>Kcal Total (Dia)</h3>
                <p id="day-total-kcal">0</p>
            </div>
        </aside>
    </div>

    <div class="modal fade" id="addFoodModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFoodModalTitle">Adicionar Alimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addFoodForm">
                        <input type="hidden" id="modal-data-refeicao" value="">
                        <input type="hidden" id="modal-tipo-refeicao" value="">
                        
                        <div class="mb-3">
                            <label for="modal-nome-alimento" class="form-label">Alimento</label>
                            <input type="text" class="form-control" id="modal-nome-alimento" required placeholder="Ex: Arroz, Maçã, Leite...">
                        </div>
                        
                        <div class="mb-3">
                            <label for="modal-quantidade" class="form-label">Quantidade</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="modal-quantidade" required placeholder="Ex: 150">
                                <select class="form-select" id="modal-unidade" style="max-width: 80px;">
                                    <option value="g" selected>g</option>
                                    <option value="ml">ml</option>
                                    <option value="u">u</option>
                                </select>
                            </div>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="saveFoodButton" style="background-color: #F8694D; border: none;">Salvar</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    //FUNÇÃO DE CORREÇÃO 
    function normalizeString(str) {
        return str.toLowerCase()
                  .normalize("NFD")
                  .replace(/[\u0300-\u036f]/g, "");
    }

    //BANCO DE DADOS DE ALIMENTOS
    const foodDatabase = {
        'arroz': 1.3, 'feijao': 0.77, 'frango': 1.65, 'peito de frango': 1.65,
        'ovo': 1.55, 'pao': 2.65, 'banana': 0.89, 'maca': 0.52, 'whey protein': 4.0,
        'alface': 0.15, 'tomate': 0.18, 'batata': 0.77, 'cafe': 2.0, 'leite': 0.42, 
        'iogurte': 0.59, 'cafe com leite': 0.5, 'suco de laranja': 0.45, 'sanduiche natural': 2.0,
        'salada de frutas': 0.6, 'iogurte grego': 0.59, 'aveia': 3.89, 'granola': 4.5, 
        'peixe': 2.0, 'carne vermelha': 2.5, 'macarrao': 1.5, 'pizza': 2.6, 'hamburguer': 2.9,
        'batata frita': 3.0, 'sorvete': 2.1, 'bolo': 3.5, 'chocolate': 5.4, 'amendoim': 5.7,
        'nozes': 6.5, 'castanha de caju': 5.6, 'chia': 4.9, 'linhaça': 5.3,
        'abacate': 1.6, 'melancia': 0.3, 'uva': 0.7, 'manga': 0.6,
        'abacaxi': 0.5, 'pera': 0.6, 'laranja': 0.47, 'kiwi': 0.61,
        'couve': 0.32, 'espinafre': 0.23, 'brócolis': 0.34, 'cenoura': 0.41,
        'beterraba': 0.43, 'ervilha': 0.81, 'milho': 0.86,
        'iogurte natural': 0.59, 'presunto': 1.5, 'queijo mussarela': 3.2, 'queijo prato': 3.4, 'queijo minas': 2.8,
        'queijo cottage': 1.7, 'peito de peru': 1.5, 'salsicha': 2.5, 'linguiça': 3.0, 'queijo cheddar': 4.0, 'pepino': 0.16,
        'abobrinha': 0.17, 'berinjela': 0.25, 'couve-flor': 0.25, 'aspargos': 0.2, 'camarão': 0.9, 'lagosta': 0.9, 'limao': 0.29,
        'tilapia': 1.0, 'bacalhau': 0.9, 'sardinha': 2.0, 'molho de tomate': 0.8, 'mel': 3.0, 'geleia': 2.5,'molho branco': 2.0,
        'salmao': 2.0, 'atum': 1.0, 'camarão': 0.9,
        'agua': 0.0, 'chá': 0.0, 'refrigerante': 0.4, 'cerveja': 0.43, 'regrigerante diet': 0.0, 'vinho': 0.85,
        'cereja': 0.5, 'damasco': 0.48, 'figo': 0.74, 'tangerina': 0.53, 'mamao': 0.43, 'pao de queijo': 3.2, 
        'pao frances': 2.7, 'croissant': 4.5, 'pao integral': 2.5, 'pipoca': 3.6,'pure de batata': 0.9, 
        'cuscuz': 1.1, 'lentilha': 1.16, 'grão de bico': 1.64, 'manteiga': 7.2, 'azeite': 8.8, 'requeijao': 3.0, 'requeijao light': 2.0,
        'creme de ricota': 2.5, 'creme de ricota light': 1.5, 'bacon': 5.4, 'biscoito de arroz': 3.8, 'biscoito integral': 4.2
    };
    
    //VARIÁVEIS GLOBAIS
    const weekNavigator = document.querySelector('.week-navigator');
    const days = document.querySelectorAll('.day');
    const feedTitle = document.getElementById('feed-title');
    const mealItems = document.querySelectorAll('.meal-item');
    const addFoodModal = new bootstrap.Modal(document.getElementById('addFoodModal'));
    const addFoodForm = document.getElementById('addFoodForm');
    const saveFoodButton = document.getElementById('saveFoodButton');
    const modalTitle = document.getElementById('addFoodModalTitle');
    const modalTipoRefeicao = document.getElementById('modal-tipo-refeicao');
    const modalDataRefeicao = document.getElementById('modal-data-refeicao');
    const dayTotalKcalEl = document.getElementById('day-total-kcal');
    let dataSelecionada = '<?php echo $dataHojeStr; ?>';

    //FUNÇÕES
    async function fetchRefeicoes(data) {
        clearAllMeals(); 
        try {
            const response = await fetch(`api_get_refeicoes.php?date=${data}`);
            if (!response.ok) throw new Error('Falha ao buscar dados.');
            const refeicoes = await response.json();
            refeicoes.forEach(refeicao => renderAlimento(refeicao)); 
            updateTotalKcal();
        } catch (error) {
            console.error('Erro:', error);
            alert('Não foi possível carregar as refeições.');
        }
    }

    async function addRefeicao() {
        const nomeAlimentoInput = document.getElementById('modal-nome-alimento').value;
        const quantidade = parseFloat(document.getElementById('modal-quantidade').value);
        const unidade = document.getElementById('modal-unidade').value;
        
        const foodKey = normalizeString(nomeAlimentoInput.trim());
        
        if (!foodDatabase.hasOwnProperty(foodKey)) {
            alert(`Alimento "${nomeAlimentoInput}" não encontrado.`);
            return;
        }
        if (!quantidade || quantidade <= 0) {
            alert('Por favor, insira uma quantidade válida.');
            return;
        }

        const kcalPerGram = foodDatabase[foodKey];
        const calculatedKcal = Math.round(kcalPerGram * quantidade);

        const data = {
            data_refeicao: modalDataRefeicao.value,
            tipo_refeicao: modalTipoRefeicao.value,
            nome_alimento: nomeAlimentoInput,
            quantidade: quantidade,
            unidade: unidade,
            kcal: calculatedKcal
        };

        try {
            const response = await fetch('api_add_refeicao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            });
            if (!response.ok) throw new Error('Falha ao salvar.');
            const novaRefeicao = await response.json();
            
            renderAlimento(novaRefeicao);
            updateTotalKcal();
            addFoodForm.reset();
            addFoodModal.hide();
        } catch (error) {
            console.error('Erro:', error);
            alert('Não foi possível salvar a refeição.');
        }
    }
    
    async function deleteRefeicao(id) {
        if (!confirm('Tem certeza que deseja excluir este alimento?')) return;
        try {
            const response = await fetch('api_delete_refeicao.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: id })
            });
            if (!response.ok) throw new Error('Falha ao excluir.');
            const result = await response.json();
            if (result.success) {
                const itemParaRemover = document.querySelector(`.meal-content li[data-id="${id}"]`);
                if (itemParaRemover) itemParaRemover.remove();
                updateTotalKcal();
            } else {
                throw new Error(result.error || 'Erro desconhecido.');
            }
        } catch (error) {
            console.error('Erro:', error);
            alert('Não foi possível excluir o alimento.');
        }
    }

    function renderAlimento(refeicao) {
        // Encontra o container
        const mealContainer = document.querySelector(`.meal-item[data-meal-type="${refeicao.tipo_refeicao}"]`);
        if (!mealContainer) return;
        
        // Encontra a <ul> DENTRO do .meal-content
        const list = mealContainer.querySelector('.meal-content ul');
        const li = document.createElement('li');
        li.setAttribute('data-id', refeicao.id);
        li.setAttribute('data-kcal', refeicao.kcal);
        
        li.innerHTML = `
            <div>
                <span>${refeicao.nome_alimento}</span>
                <span class="food-qty">(${refeicao.quantidade}${refeicao.unidade})</span>
            </div>
            <div>
                <span>${refeicao.kcal} kcal</span>
                <i class="fa-solid fa-trash-can delete-food-btn"></i>
            </div>
        `;
        list.appendChild(li);
    }
    
    function clearAllMeals() {
        mealItems.forEach(item => {

            const content = item.querySelector('.meal-content'); 
            if (content) { 
                content.querySelector('ul').innerHTML = '';
                content.querySelector('.meal-total-kcal').textContent = '0 kcal';
            }
        });
        dayTotalKcalEl.textContent = '0'; // <- Contador zera
    }

    function updateTotalKcal() {
        let totalDia = 0;
        mealItems.forEach(item => {
            let totalRefeicao = 0;
            const content = item.querySelector('.meal-content');
            if (content) {
                const alimentos = content.querySelectorAll('li');
                alimentos.forEach(alimento => {
                    totalRefeicao += parseInt(alimento.dataset.kcal) || 0;
                });
                
                const totalKcalEl = content.querySelector('.meal-total-kcal');
                if (totalKcalEl) { 
                    totalKcalEl.textContent = `${totalRefeicao} kcal`;
                }
            }
            totalDia += totalRefeicao;
        });
        dayTotalKcalEl.textContent = totalDia;
    }

    //EVENTOS 
    days.forEach(day => {
        day.addEventListener('click', () => {
            days.forEach(d => d.classList.remove('active'));
            day.classList.add('active');
            dataSelecionada = day.dataset.date;
            feedTitle.textContent = dataSelecionada == '<?php echo $dataHojeStr; ?>' ? 'Hoje' : day.dataset.dayName;
            fetchRefeicoes(dataSelecionada);
        });
    });

    mealItems.forEach(item => {
        const plusButton = item.querySelector('.add-food-btn');
        const mealType = item.dataset.mealType;
        if(plusButton) {
            plusButton.addEventListener('click', (e) => {
                e.stopPropagation(); 
                modalTitle.textContent = `Adicionar em ${mealType}`;
                modalTipoRefeicao.value = mealType;
                modalDataRefeicao.value = dataSelecionada;
                addFoodForm.reset();
                addFoodModal.show();
            });
        }
    });

    mealItems.forEach(item => {
        const header = item.querySelector('.meal-header');
        const content = item.querySelector('.meal-content');
        if (header && content) {
            header.addEventListener('click', () => {
                if (content.style.display === 'block') {
                    content.style.display = 'none';
                } else {
                    content.style.display = 'block';
                }
            });
        }
    });

    saveFoodButton.addEventListener('click', addRefeicao);

    document.querySelector('.meals-list').addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-food-btn')) {
            const itemLi = e.target.closest('li[data-id]');
            if (itemLi) {
                const idParaExcluir = itemLi.dataset.id;
                deleteRefeicao(idParaExcluir);
            }
        }
    });

    // INICIALIZAÇÃO
    fetchRefeicoes(dataSelecionada);
});
</script>

</body>
</html>