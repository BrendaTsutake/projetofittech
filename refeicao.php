<?php
// 1. Inicia a sessão e configura fuso horário
date_default_timezone_set('America/Sao_Paulo');
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// 2. Conecta ao banco
$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) { die("Connection failed: " . $conn->connect_error); }

$id_usuario = $_SESSION['id'];

// 3. Busca dados para cálculo
$sql_user = "SELECT peso_atual, altura, idade, genero, frequencia_exercicio, objetivo FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql_user);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result_user = $stmt->get_result();
$user = $result_user->fetch_assoc();

// 4. Cálculos de Meta
$peso   = ($user['peso_atual'] > 0) ? $user['peso_atual'] : 70; 
$altura = ($user['altura'] > 0) ? $user['altura'] : 170;
$idade  = ($user['idade'] > 0) ? $user['idade'] : 30;
$genero = $user['genero'] ? strtolower($user['genero']) : 'masculino';
$atividade = $user['frequencia_exercicio'];
$objetivo = $user['objetivo'];

if ($genero == 'feminino' || $genero == 'mulher') {
    $tmb = 447.6 + (9.2 * $peso) + (3.1 * $altura) - (4.3 * $idade);
} else {
    $tmb = 88.36 + (13.4 * $peso) + (4.8 * $altura) - (5.7 * $idade);
}

$fator = 1.2; 
if ($atividade == 'Levemente ativo') $fator = 1.375;
if ($atividade == 'Ativo') $fator = 1.55;
if ($atividade == 'Muito ativo') $fator = 1.725;

$gasto_total = $tmb * $fator;
$meta_diaria = $gasto_total; 
if (stripos($objetivo, 'Perder') !== false) { $meta_diaria -= 500; } 
elseif (stripos($objetivo, 'Ganhar') !== false) { $meta_diaria += 500; }

$meta_diaria = round(max(1200, $meta_diaria));
$meta_semanal = $meta_diaria * 7;

$conn->close();

// Lógica dos dias
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="icon" href="img/logo-img.png" type="image/png">
    <style>
    @font-face { font-family: "Louis George Cafe"; src: url(fontes/louis_george_cafe/Louis\ George\ Cafe\ Light.ttf) format("truetype"); }
    @font-face { font-family: "mousse"; src: url("fontes/mousse/Mousse-Regular.otf") format("otf"); }
    body { font-family: 'Louis George Cafe', Arial, sans-serif; font-weight: 500; font-size: 22px; background-color: #FFF9EA; margin: 0; min-height: 100vh; }
    .container { display: grid; grid-template-columns: 250px 1fr 300px; gap: 20px; max-width: 1400px; margin: 0 auto; padding: 20px; }
    .sidebar-left { position: sticky; top: 20px; height: 95vh; }
    .sidebar-left .logo img { width: 150px; margin-bottom: 30px; }
    .sidebar-left ul { list-style: none; padding: 0; }
    .sidebar-left ul li a { display: flex; align-items: center; padding: 15px; text-decoration: none; color: #555; font-size: 18px; border-radius: 8px; margin-bottom: 10px; font-weight: bold; }
    .sidebar-left ul li a i { margin-right: 15px; width: 20px; }
    .sidebar-left ul li a:hover, .sidebar-left ul li a.active { background-color: #F8694D; color: white; }
    .sidebar-right { position: sticky; top: 20px; }
    .user-tools { background-color: #C8E6C9; padding: 15px; border-radius: 12px; width: 400px; margin-bottom: 20px; }
    .search-bar { display: flex; align-items: center; background-color: white; padding: 8px; border-radius: 20px; margin-bottom: 20px; }
    .search-bar input { border: none; outline: none; background: none; width: 100%; margin-left: 8px; }
    .tool-icons { display: flex; justify-content: space-around; }
    .tool-icons a { font-size: 22px; color: #333; }
    .feed-content { padding: 10px; }
    .feed-header { text-align: center; margin-bottom: 30px; font-weight: bold; color: #555; font-size: 40px; text-transform: capitalize; }
    .week-navigator { display: flex; justify-content: space-around; align-items: center; margin-bottom: 40px; }
    .day { display: flex; flex-direction: column; align-items: center; cursor: pointer; }
    .day-circle { width: 45px; height: 45px; border-radius: 50%; border: 2px solid #ccc; display: flex; justify-content: center; align-items: center; margin-bottom: 8px; transition: all 0.2s; font-weight: bold; }
    .day span { font-size: 18px; text-transform: uppercase; font-weight: bold; color: #888; }
    .day.active .day-circle { border-color: #F8694D; background-color: #F8694D; color: white; }
    .day.active span { color: #F8694D; }
    .meal-item { background-color: #E8F5E9; border-radius: 15px; margin-bottom: 15px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); }
    .meal-header { display: flex; align-items: center; justify-content: space-between; padding: 20px; cursor: pointer; transition: transform 0.2s; }
    .meal-header:hover { transform: translateY(-3px); }
    .meal-info { display: flex; align-items: center; gap: 15px; }
    .meal-info i { font-size: 26px; color: #555; }
    .meal-info .meal-name { font-size: 20px; font-weight: bold; color: #333; }
    .meal-header > i.fa-plus { font-size: 22px; color: #555; cursor: pointer; z-index: 10; }
    .meal-content { padding: 0 20px 20px 20px; display: none; }
    .meal-content ul { list-style: none; padding: 0; margin: 0 0 15px 0; }
    .meal-content li { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #d4e9d5; font-size: 18px; }
    .meal-content li span:first-child { font-weight: bold; color: #333; }
    .meal-content li span:last-child { color: #F8694D; font-weight: bold; }
    .meal-content li .food-qty { color: #555; font-size: 16px; margin-left: 10px; }
    .meal-content li i { cursor: pointer; color: #888; margin-left: 10px; }
    .meal-content li i:hover { color: #F8694D; }
    .meal-footer { display: flex; justify-content: space-between; font-size: 18px; font-weight: bold; color: #333; }
    .total-kcal-card { background-color: #F8694D; padding: 20px; border-radius: 12px; text-align: center; margin-bottom: 20px; }
    .total-kcal-card.weekly { background-color: #9EC662; }
    .total-kcal-card h3 { font-family: 'mousse', Arial, sans-serif; color: #FFF9EA; font-size: 26px; margin: 0; }
    .total-kcal-card p { font-size: 38px; font-weight: bold; color: #FFF9EA; margin: 0; }
    .total-kcal-card span.meta-text { font-size: 20px; opacity: 0.8; }
    .btn-manual { background-color: #9EC662; color: white; width: 100%; margin-bottom: 20px; border: none; font-weight: bold; padding: 12px; border-radius: 10px; }
    .btn-manual:hover { background-color: #86a754; color: white; }
    </style>
</head>
<body>

    <div class="container">
        <nav class="sidebar-left">
            <div class="logo"><img src="img/logo.png" alt="FitTech Logo"></div>
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
                    <div class="day <?php echo $dia['ativo'] ? 'active' : ''; ?>" onclick="trocarDia('<?php echo $dia['data']; ?>', '<?php echo $dia['nome']; ?>', this)">
                        <div class="day-circle"><?php echo $dia['letra']; ?></div>
                        <span><?php echo $dia['nome']; ?></span>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="meals-list">
                <?php 
                $tipos = ['Café da Manhã' => 'fa-sun', 'Almoço' => 'fa-cloud-sun', 'Jantar' => 'fa-moon', 'Snacks/Outros' => 'fa-cookie-bite'];
                foreach ($tipos as $nome => $icone): 
                ?>
                <div class="meal-item" data-meal-type="<?php echo $nome; ?>">
                    <div class="meal-header" onclick="toggleRefeicao(this)">
                        <div class="meal-info">
                            <i class="fa-solid <?php echo $icone; ?>"></i>
                            <span class="meal-name"><?php echo $nome; ?></span>
                        </div>
                        <i class="fa-solid fa-plus add-food-btn" onclick="abrirModalAuto('<?php echo $nome; ?>', event)"></i>
                    </div>
                    <div class="meal-content">
                        <ul></ul>
                        <div class="meal-footer">
                            <span>Total Refeição</span>
                            <span class="meal-total-kcal">0 kcal</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </main>

        <aside class="sidebar-right">
            <div class="user-tools">
                <div class="search-bar"><i class="fa-solid fa-magnifying-glass"></i> <input type="text" placeholder="Buscar..."></div>
                <div class="tool-icons">
                    <a href="calendario.php"><i class="fa-solid fa-calendar-days"></i></a>
                    <a href="perfil.php"><i class="fa-solid fa-user"></i></a>
                </div>
            </div>
            
            <div class="total-kcal-card">
                <h3>Kcal Total (Dia)</h3>
                <p id="day-total-kcal">0 / <span class="meta-text"><?php echo $meta_diaria; ?></span></p>
            </div>
            <div class="total-kcal-card weekly">
                <h3>Kcal da Semana</h3>
                <p id="week-total-kcal">0 / <span class="meta-text"><?php echo $meta_semanal; ?></span></p>
            </div>
        </aside>
    </div>

    <div class="modal fade" id="addFoodModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addFoodModalTitle">Adicionar Alimento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <button class="btn btn-manual" onclick="openManualModal()">
                        <i class="fa-solid fa-barcode"></i> Produto de Mercado / Manual
                    </button>
                    <div style="display: flex; align-items: center; margin: 15px 0;">
                        <div style="flex-grow:1; border-top: 1px solid #ddd;"></div>
                        <span style="padding: 0 10px; color: #777; font-size: 14px;">Ou busque na base</span>
                        <div style="flex-grow:1; border-top: 1px solid #ddd;"></div>
                    </div>
                    <form id="addFoodForm">
                        <input type="hidden" id="modal-data-refeicao">
                        <input type="hidden" id="modal-tipo-refeicao">
                        <div class="mb-3">
                            <label class="form-label">Alimento</label>
                            <input type="text" class="form-control" id="modal-nome-alimento" placeholder="Ex: Arroz">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantidade</label>
                            <div class="input-group">
                                <input type="number" class="form-control" id="modal-quantidade" placeholder="150">
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
                    <button type="button" class="btn btn-primary" onclick="addRefeicaoAuto()" style="background-color: #F8694D; border: none;">Salvar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addManualModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #9EC662; color: white;">
                    <h5 class="modal-title">Produto Industrializado</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p style="font-size:14px; color:#666; text-align:center; margin-bottom:20px;">
                        <i class="fa-solid fa-circle-info"></i> Olhe a <strong>Tabela Nutricional</strong> da embalagem e digite as calorias totais que você consumiu.
                    </p>
                    <form id="addManualForm">
                        <input type="hidden" id="manual-data-refeicao">
                        <input type="hidden" id="manual-tipo-refeicao">
                        <div class="mb-3">
                            <label class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="manual-nome" placeholder="Ex: Barra de Cereal">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Calorias Totais (Kcal)</label>
                            <input type="number" class="form-control" id="manual-kcal" placeholder="Ex: 90">
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="backToAutoModal()">Voltar</button>
                    <button type="button" class="btn btn-primary" onclick="addManualRefeicao()" style="background-color: #F8694D; border: none;">Salvar Produto</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const META_DIARIA = <?php echo $meta_diaria; ?>;
    const META_SEMANAL = <?php echo $meta_semanal; ?>;
    const DATA_HOJE_PHP = '<?php echo $dataHojeStr; ?>';
    let dataSelecionada = DATA_HOJE_PHP;

    let modalAuto = new bootstrap.Modal(document.getElementById('addFoodModal'));
    let modalManual = new bootstrap.Modal(document.getElementById('addManualModal'));

    // --- FUNÇÃO PARA FORMATAR DATA BONITA ---
    function formatarDataBonita(dataISO) {
        const partes = dataISO.split('-');
        const data = new Date(partes[0], partes[1] - 1, partes[2]);
        
        const opcoes = { weekday: 'long', day: 'numeric', month: 'long' };
        let texto = data.toLocaleDateString('pt-BR', opcoes);
        
        // Capitaliza a primeira letra
        texto = texto.charAt(0).toUpperCase() + texto.slice(1);

        // Adiciona "Hoje -" se for a data atual
        if (dataISO === DATA_HOJE_PHP) {
            return "Hoje - " + texto;
        }
        return texto;
    }
    
    // Atualiza o título inicial
    document.getElementById('feed-title').textContent = formatarDataBonita(dataSelecionada);

    function normalizeString(str) {
        return str.toLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "");
    }

    const foodDatabase = {
'arroz': 1.3, 'feijao preto': 1.32, 'feijao carioca': 1.54, 'feijao branco': 1.6, 'feijao vermelho': 1.76, 'peito de frango': 1.6, 
        'coxa de frango': 1.7,'sobrecoxa de frango': 2.4, 'moela de frango': 1.3,'coração de frango': 2.0, 'frango a milanesa': 3.61,
        'frango desfiado': 0.195, 'ovo pequeno': 57, 'ovo medio': 72, 'ovo grande': 77, 'ovo mexido': 1.66, 'omelete': 2.7,
        'pao frances': 140, 'pao de forma': 140, 'pao integral': 125, 'banana nanica': 92, 'banana terra': 218 , 'banana prata': 105, 
        'banana maçã': 72,'maca pequena': 56,'maca média': 72,'maca grande': 111, 'maca verde pequena': 55, 'maca verde média': 72,
        'maca verde grande': 110, 'whey protein': 4.0, 'alface': 0.15,'tomate': 0.18,'tomate cereja': 0.18,'batata cozida': 0.52,
        'cafe': 0.2,'leite desnatado':0.37,'leite integral': 0.62,'leite semidesnatado': 0.36, 'iogurte natural desnatado': 0.60,
        'iogurte naturalintegral' :0.66,'iogurte de fruta': 1.04,'iogurte zero açúcar': 0.52,'cafe com leite': 0.65,'suco de laranja': 0.47,
        'salada de frutas': 0.65, 'sanduiche natural': 2.0,'iogurte grego': 1.43, 'aveia': 4.0, 'granola': 4.5, 'peixe grelhado': 1.3,
        'peixe a milanesa': 2.32,'salmão grelhado': 2.20,'tilápia grelhada': 0.96, 'pintado assado': 1.92,'salmão cru': 2.08,'atum cru': 1.4,
        'panga': 0.64,'pescada branca': 1.11,'camarão': 1.19,'merluza': 1.48,'sardinha' :2.08,'patinho': 1.33,'lagarto': 1.35, 'fígado bovino': 1.41,
        'picanha': 2.33,'alcatra': 2.44,'capa de contra-filé': 3.0,'maminha': 1.53,'carne vermelha': 2.5,'macarrao de trigo branco': 1.75,
        'macarrao integral':1.50,'macarrao de quinoa': 1.80, 'macarrao de espelta': 1.80,'pizza de calabresa': 2.53,'pizza napolitana': 2.08,
        'pizza de frango com catupiry': 2.14,'pizza marguerita': 2.75,'pizza namorados': 2.28,'hamburguer': 3.19, 'batata frita (oleo)': 3.12,
        'batata frita air-fryer': 1.40,'batata doce': 0.86,'nozes': 6.5,'castanha de caju': 6.0,'chia': 4.9,'linhaça': 5.3,'abacate': 1.6,
        'melancia': 0.3,'azeite':9.0, 'uva': 2.5,'manga': 202,'abacaxi': 0.5, 'pera': 100, 'laranja': 202, 'kiwi': 46,'couve': 0.66, 
        'espinafre': 0.40, 'brocolis': 0.34, 'cenoura crua': 0.41,'cenoura cozida': 0.35,'beterraba crua': 0.5, 'beterraba cozida': 0.4,
        'ervilha': 0.81,'milho': 0.86,'presunto': 1.22, 'peito de peru': 1.10,'queijo mussarela': 3.2, 'queijo prato': 3.4, 'queijo minas': 2.8,
        'queijo cottage': 1.3, 'queijo cheddar': 4.0, 'pepino': 0.15,'salsicha': 2.5, 'linguiça toscana': 2.1, 'linguiça calabresa': 3.2,
        'abobrinha': 0.17, 'berinjela crua': 0.25,'berinjela grelhada': 1.0, 'couve-flor': 0.25, 'aspargos': 0.20, 'camarão': 0.9, 
        'lagosta a vapor': 1.0,'lagosta grelhada': 3.08,'limao': 0.29,'bacalhau cozido': 0.84,'bacalhau assado': 1.10,'molho de tomate': 0.37,
        'molho branco': 1.47,'mel': 3.0, 'geleia': 2.66,'agua': 0.0,'cha': 0.0, 'cereja': 3.3,'damasco fresco': 0.48, 'damasco seco': 2.4,  
        'cerveja': 0.43,'refrigerante': 0.42,  'refrigerante diet': 0.0, 'vinho': 0.83,'figo cru': 0.74,'figo seco': 2.7, 'tangerina': 45, 
        'mamao': 800, 'pao de queijo': 3.2, 'croissant': 4.5, 'pipoca sem oleo': 3.87,'pipoca com oleo': 4.5,'pure de batata': 0.83, 
        'cuscuz': 1.3,'cuscuz paulista': 1.8,'cuscuz marroquino': 3.5, 'lentilha': 1.16, 'grão de bico': 1.8, 'manteiga': 7.0, 
        'requeijao':2.5, 'requeijao light': 1.5,'creme de ricota':1.86, 'creme de ricota light': 1.08, 'bacon': 4.3, 'biscoito de arroz': 3.8, 
        'biscoito integral': 4.0, 'morango': 8, 'acucar': 4.0
    };

    window.openManualModal = function() {
        document.getElementById('manual-data-refeicao').value = document.getElementById('modal-data-refeicao').value;
        document.getElementById('manual-tipo-refeicao').value = document.getElementById('modal-tipo-refeicao').value;
        modalAuto.hide();
        setTimeout(() => { modalManual.show(); }, 400);
    }

    window.backToAutoModal = function() {
        modalManual.hide();
        setTimeout(() => { modalAuto.show(); }, 400);
    }

    async function saveToApi(payload, modalInstance) {
        try {
            const res = await fetch('api_add_refeicao.php', {
                method: 'POST', headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(payload)
            });
            
            const text = await res.text();
            try {
                const json = JSON.parse(text);
                if (json.success) {
                    fetchRefeicoes(dataSelecionada);
                    modalInstance.hide();
                    document.getElementById('addFoodForm').reset();
                    document.getElementById('addManualForm').reset();
                } else {
                    alert('Erro do servidor: ' + json.error);
                }
            } catch (e) {
                console.error(text);
                alert('Erro inesperado no PHP. Verifique o console.');
            }
        } catch (e) { console.error(e); alert('Erro de conexão.'); }
    }

    window.addRefeicaoAuto = function() {
        const nome = document.getElementById('modal-nome-alimento').value;
        const qtd = parseFloat(document.getElementById('modal-quantidade').value);
        const uni = document.getElementById('modal-unidade').value;
        const key = normalizeString(nome.trim());

        if (!foodDatabase.hasOwnProperty(key)) {
            alert('Alimento não encontrado na base. Use o botão azul "Produto de Mercado".');
            return;
        }
        if (!qtd) { alert('Digite a quantidade.'); return; }

        const kcal = Math.round(foodDatabase[key] * qtd);
        
        saveToApi({
            data_refeicao: document.getElementById('modal-data-refeicao').value,
            tipo_refeicao: document.getElementById('modal-tipo-refeicao').value,
            nome_alimento: nome,
            quantidade: qtd,
            unidade: uni,
            kcal: kcal
        }, modalAuto);
    }

    window.addManualRefeicao = function() {
        const nome = document.getElementById('manual-nome').value;
        const kcal = parseInt(document.getElementById('manual-kcal').value);

        if (!nome || !kcal) { alert('Preencha nome e calorias.'); return; }

        saveToApi({
            data_refeicao: document.getElementById('manual-data-refeicao').value,
            tipo_refeicao: document.getElementById('manual-tipo-refeicao').value,
            nome_alimento: nome + " (Prod.)",
            quantidade: 1,
            unidade: 'unid',
            kcal: kcal
        }, modalManual);
    }

    async function fetchRefeicoes(data) {
        document.querySelectorAll('.meal-content ul').forEach(ul => ul.innerHTML = '');
        document.querySelectorAll('.meal-total-kcal').forEach(span => span.textContent = '0 kcal');
        document.getElementById('day-total-kcal').innerHTML = `0 / <span class="meta-text">${META_DIARIA}</span>`;
        
        try {
            const res = await fetch(`api_get_refeicoes.php?date=${data}`);
            const json = await res.json();
            json.forEach(renderAlimento);
            updateTotalKcal();
            updateWeeklyKcal(data);
        } catch (e) { console.error(e); }
    }

    async function updateWeeklyKcal(data) {
        try {
            const res = await fetch(`api_get_weekly_calories.php?date=${data}`);
            const json = await res.json();
            const total = json.total || 0;
            document.getElementById('week-total-kcal').innerHTML = `${total} / <span class="meta-text">${META_SEMANAL}</span>`;
        } catch (e) { console.error(e); }
    }

    function renderAlimento(refeicao) {
        const container = document.querySelector(`.meal-item[data-meal-type="${refeicao.tipo_refeicao}"]`);
        if (!container) return;
        const ul = container.querySelector('ul');
        const li = document.createElement('li');
        li.setAttribute('data-id', refeicao.id);
        li.setAttribute('data-kcal', refeicao.kcal);
        li.innerHTML = `<div><span>${refeicao.nome_alimento}</span> <span style="color:#555;font-size:14px">(${refeicao.quantidade}${refeicao.unidade})</span></div> <div><span>${refeicao.kcal} kcal</span> <i class="fa-solid fa-trash-can delete-food-btn" style="cursor:pointer;margin-left:10px;color:#888" onclick="deleteRefeicao(${refeicao.id})"></i></div>`;
        ul.appendChild(li);
    }

    function updateTotalKcal() {
        let totalDia = 0;
        document.querySelectorAll('.meal-item').forEach(item => {
            let totalRef = 0;
            item.querySelectorAll('li').forEach(li => totalRef += parseInt(li.dataset.kcal));
            item.querySelector('.meal-total-kcal').textContent = totalRef + ' kcal';
            totalDia += totalRef;
        });
        document.getElementById('day-total-kcal').innerHTML = `${totalDia} / <span class="meta-text">${META_DIARIA}</span>`;
    }

    // --- EVENTOS ---
    
    window.trocarDia = function(data, nome, el) {
        document.querySelectorAll('.day').forEach(x => x.classList.remove('active'));
        el.classList.add('active');
        
        // Atualiza Data
        dataSelecionada = data;
        
        // MUDANÇA: Atualiza Título com data bonita
        document.getElementById('feed-title').textContent = formatarDataBonita(dataSelecionada);
        
        fetchRefeicoes(dataSelecionada);
    }

    window.abrirModalAuto = function(tipo, event) {
        if(event) event.stopPropagation(); 
        document.getElementById('modal-tipo-refeicao').value = tipo;
        document.getElementById('modal-data-refeicao').value = dataSelecionada;
        document.getElementById('addFoodForm').reset();
        modalAuto.show();
    }

    window.toggleRefeicao = function(el) {
        const content = el.nextElementSibling;
        content.style.display = (content.style.display === 'block') ? 'none' : 'block';
    }

    window.deleteRefeicao = async function(id) {
        if (!confirm('Excluir?')) return;
        try {
            await fetch('api_delete_refeicao.php', {
                method: 'POST', headers: {'Content-Type':'application/json'},
                body: JSON.stringify({id: id})
            });
            fetchRefeicoes(dataSelecionada);
        } catch(err) { console.error(err); }
    }

    fetchRefeicoes(dataSelecionada);
});
</script>
</body>
</html>