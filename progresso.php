<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

$servername = "localhost";
$username_db = "root";
$password_db = "";
$dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$id_usuario = $_SESSION['id'];
$sql = "SELECT nome, peso_atual, peso_inicial, objetivo FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$conn->close();

// Define as variáveis para exibição
$objetivo_display = $user['objetivo'] ? htmlspecialchars($user['objetivo']) : 'Defina uma meta';
$peso_atual_display = $user['peso_atual'] ? htmlspecialchars($user['peso_atual']) : '0';
$peso_inicial_display = $user['peso_inicial'] ? htmlspecialchars($user['peso_inicial']) : '0';

// Define as variáveis para o formulário
$objetivo_form = $user['objetivo'] ? htmlspecialchars($user['objetivo']) : '';
$peso_atual_form = $user['peso_atual'] ? htmlspecialchars($user['peso_atual']) : '';
$peso_inicial_form = $user['peso_inicial'] ? htmlspecialchars($user['peso_inicial']) : '';


// Define a data
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
$data_hoje = strftime('%d de %B de %Y');
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Progresso</title> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
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
        font-size: 22px;
        background-color: #FFF9EA;
        margin: 0;
        min-height: 100vh;
    }
    .container {
        display: grid;
        width: 100%;
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
    .botao button {
        color: #FFD966;
        border: 2px solid #FFD966; 
        border-radius: 10px;
        padding: 7px 15px;
        background: transparent;
        text-align: center; 
        font-weight: bold;
    }
    .botao button:hover {
        background-color: #FFD966;
        transition: 0.4s;
        color: #FFF9EA;
    }
    .conteudo {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 25px;
    }
    .data {
        margin-top: 50px;
        background-color: #9EC662;
        color: #FFF9EA;
        font-weight: bold;
        padding: 10px 30px;
        border-radius: 40px;
        text-align: center;
        width: 500px;
        font-size: 25px;
        margin-bottom: 50px;
    }
    .data p, .meta p, .pesos p {
        margin-bottom: 0;
    }
    .meta {
        background-color: #F8694D;
        color: #FFF9EA;
        border-radius: 10px;
        text-align: center;
        padding: 20px;
        width: 400px;
        font-size: 30px;
        margin-bottom: 30px;
    }
    .pesos {
        display: flex;
        justify-content: center;
        gap: 20px;
    }
    .peso-atual, .peso-inicial {
        background-color: #FFD966;
        color: #FFF9EA;
        border-radius: 10px;
        text-align: center;
        padding: 20px;
        width: 200px;
        margin-bottom: 20px;
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
                <li><a href="refeicao.php"><i class="fa-solid fa-utensils"></i> <span>Refeições</span></a></li>
                <li><a href="dicas.php"><i class="fa-solid fa-pencil"></i> <span>Dica do Nutri</span></a></li>
                <li><a href="progresso.php" class="active"><i class="fa-solid fa-chart-line"></i> <span>Progresso</span></a></li>
            </ul>
        </nav>

        <div class="conteudo">
            <div class="data">
                <p><?php echo $data_hoje; ?></p>
            </div>

            <div class="meta">
                <h5>Sua Meta</h5>
                <p><?php echo $objetivo_display; ?></p>
            </div>

            <div class="pesos">
                <div class="peso-atual">
                    <h5>Peso Atual</h5>
                    <p><?php echo $peso_atual_display; ?> KG</p>
                </div>
                <div class="peso-inicial">
                    <h5>Peso Inicial</h5>
                    <p><?php echo $peso_inicial_display; ?> KG</p>
                </div>
            </div>

            <div class="botao">
                <button data-bs-toggle="modal" data-bs-target="#editModal">Editar</button>
            </div>
        </div>

        <aside class="sidebar-right">
            <div class="user-tools">
                
                <div class="search-bar" style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" placeholder="Buscar usuários..." autocomplete="off">
                    
                    <div id="searchResults" class="search-dropdown"></div>
                </div>

                <div class="tool-icons">
                    <a href="calendario.php" title="Calendário"><i class="fa-solid fa-calendar-days"></i></a>
                    <a href="perfil.php" title="Perfil"><i class="fa-solid fa-user"></i></a>
                </div>
            </div>
            
            <?php if(basename($_SERVER['PHP_SELF']) == 'paginicial.php' || basename($_SERVER['PHP_SELF']) == 'refeicao.php'): ?>
            <div class="total-kcal-card" style="margin-top: 20px;">
                <h3>Kcal Total (Dia)</h3>
                <p id="day-total-kcal">0 / <span class="meta-text"><?php echo isset($meta_diaria) ? $meta_diaria : '0'; ?></span></p>
            </div>
            <?php endif; ?>
        </aside>

<style>
.search-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    width: 100%;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    z-index: 1000;
    margin-top: 5px;
    display: none;
    overflow: hidden;
}

.search-item {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    text-decoration: none;
    color: #333;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s;
}

.search-item:last-child {
    border-bottom: none;
}

.search-item:hover {
    background-color: #FFF9EA;
    color: #F8694D;
}

.search-avatar {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 10px;
    border: 1px solid #F8694D;
}

.search-name {
    font-size: 16px;
    font-weight: bold;
    font-family: 'Louis George Cafe', sans-serif;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const searchResults = document.getElementById('searchResults');

    searchInput.addEventListener('input', async function() {
        const termo = this.value.trim();

        if (termo.length < 2) {
            searchResults.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`api_search_users.php?q=${termo}`);
            const users = await response.json();

            searchResults.innerHTML = ''; // Limpa resultados anteriores

            if (users.length > 0) {
                users.forEach(user => {
                    // Cria o link para o perfil do usuário
                    const link = document.createElement('a');
                    link.href = `perfil_usuario.php?id=${user.id}`;
                    link.className = 'search-item';
                    link.innerHTML = `
                        <img src="${user.profile_pic}" class="search-avatar" onerror="this.src='https://cdn-icons-png.flaticon.com/512/847/847969.png'">
                        <span class="search-name">${user.username}</span>
                    `;
                    searchResults.appendChild(link);
                });
                searchResults.style.display = 'block';
            } else {
                searchResults.innerHTML = '<div style="padding:10px; text-align:center; color:#777;">Usuário não encontrado</div>';
                searchResults.style.display = 'block';
            }

        } catch (error) {
            console.error('Erro na busca:', error);
        }
    });

    // Fecha a lista se clicar fora
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });
});
</script>
        </aside>
    </div>

    <div class="modal fade" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Informações</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                
                <form action="api_update_perfil.php" method="POST">
                    <div class="modal-body">
                        
                        <div class="mb-3">
                            <label for="peso_atual" class="form-label">Peso Atual (KG)</label>
                            <input type="text" class="form-control" name="peso_atual" id="peso_atual" value="<?php echo $peso_atual_form; ?>" placeholder="Ex: 75.5" required>
                        </div>

                        <div class="mb-3">
                            <label for="peso_inicial" class="form-label">Peso Inicial (KG)</label>
                            <input type="text" class="form-control" name="peso_inicial" id="peso_inicial" value="<?php echo $peso_inicial_form; ?>" placeholder="Ex: 80.0" required>
                        </div>

                        <div class="mb-3">
                            <label for="objetivo" class="form-label">Sua Meta</label>
                            <input type="text" class="form-control" name="objetivo" id="objetivo" value="<?php echo $objetivo_form; ?>" placeholder="Ex: Perder peso" required>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #F8694D; border: none;">Salvar Mudanças</button>
                    </div>
                </form> </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>
</html>