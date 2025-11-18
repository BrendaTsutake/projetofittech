<?php
//Inicia a sessão
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

//Verifica se tem ID na URL
if (!isset($_GET['id'])) {
    header("Location: paginicial.php");
    exit;
}

$id_perfil = intval($_GET['id']);
$id_logado = $_SESSION['id'];

//Se o ID for o meu mesmo, vá para o perfil do usuário editável
if ($id_perfil === $id_logado) {
    header("Location: perfil.php");
    exit;
}

//Conecta ao banco
$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

//Busca dados do USUÁRIO VISITADO
$sql_user = "SELECT username, COALESCE(bio, '') as bio, profile_pic FROM usuarios WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $id_perfil);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

//Se usuário não existe
if (!$user) {
    echo "Usuário não encontrado.";
    exit;
}

//Busca os POSTS desse usuário
$sql_posts = "SELECT imagem_path, caption FROM postagens WHERE id_usuario = ? ORDER BY data_postagem DESC";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $id_perfil);
$stmt_posts->execute();
$result_posts = $stmt_posts->get_result();
$postagens = [];
while ($row = $result_posts->fetch_assoc()) {
    $postagens[] = $row;
}
$num_postagens = count($postagens);

$stmt_user->close();
$stmt_posts->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo htmlspecialchars($user['username']); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        font-size: 20px; 
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
    .tool-icons a:hover { 
        color: #F8694D;
    }
    
    .profile-content { 
        padding: 10px; 
    }
    .profile-header { 
        display: flex; 
        align-items: center; 
        gap: 30px; 
        margin-bottom: 30px; 
        padding-bottom: 30px; 
        border-bottom: 1px solid #e0e0e0; 
    }
    .profile-avatar { 
        position: relative; 
        flex-shrink: 0; 
    }
    .avatar-img { 
        width: 150px; 
        height: 150px; 
        border-radius: 50%; 
        border: 4px solid #C8E6C9; 
        display: flex; 
        justify-content: center; 
        align-items: center; 
        background-color: #f0f0f0; 
        overflow: hidden; 
    }
    .avatar-img img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
    }
    
    .profile-info .username { 
        font-size: 28px; 
        font-weight: bold; 
        margin: 0; 
    }
    .profile-info .stats { 
        font-size: 18px; 
        margin: 5px 0 15px 0; 
        color: #555; 
    }
    .profile-info .bio { 
        font-family: 'Louis George Cafe', sans-serif; 
        font-size: 15px; 
        color: #777; 
        line-height: 1.6; 
    }
    
    /* Botão Voltar */
    .btn-back { 
        background-color: #e0e0e0; 
        border: none; 
        padding: 8px 20px; 
        border-radius: 10px; 
        font-weight: bold; 
        color: #333; 
        cursor: pointer; 
        margin-top: 15px; 
    }
    .btn-back:hover { 
        background-color: #d0d0d0; 
    }

    .diet-gallery-header { 
        margin-bottom: 20px; 
    }
    .diet-gallery h2 { 
        font-size: 24px; 
        font-weight: bold; 
        color: #333; margin: 0; 
    }
    
    .photo-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); 
        gap: 15px; 
    }
    .photo-item { 
        aspect-ratio: 1 / 1; 
        background-color: #e0e0e0; 
        border-radius: 12px; 
        overflow: hidden; 
        cursor: pointer; 
    }
    .photo-item img { 
        width: 100%; 
        height: 100%; 
        object-fit: cover; 
        transition: transform 0.3s ease; 
    }
    .photo-item:hover img { 
        transform: scale(1.05); 
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
                <li><a href="progresso.php"><i class="fa-solid fa-chart-line"></i> <span>Progresso</span></a></li>
            </ul>
        </nav>

        <main class="profile-content">
            <header class="profile-header">
                <div class="profile-avatar">
                    <div class="avatar-img">
                        <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Foto de Perfil">
                    </div>
                    </div>
                <div class="profile-info">
                    <h1 class="username"><?php echo htmlspecialchars($user['username']); ?></h1>
                    <p class="stats"><strong><?php echo $num_postagens; ?></strong> Publicações</p>
                    <p class="bio">
                        <?php echo htmlspecialchars($user['bio']) ?: 'Este usuário ainda não adicionou uma bio.'; ?>
                    </p>
                    
                    <button class="btn-back" onclick="window.history.back()">Voltar</button>
                </div>
            </header>

            <section class="diet-gallery">
                <div class="diet-gallery-header">
                    <h2>Dieta de <?php echo htmlspecialchars($user['username']); ?></h2>
                    </div>
                <div class="photo-grid">
                    
                    <?php foreach ($postagens as $post): ?>
                    <div class="photo-item" 
                        data-bs-toggle="modal" 
                        data-bs-target="#viewPostModal"
                        data-img-src="<?php echo htmlspecialchars($post['imagem_path']); ?>"
                        data-caption="<?php echo htmlspecialchars($post['caption']); ?>">
                        
                        <img src="<?php echo htmlspecialchars($post['imagem_path']); ?>" alt="<?php echo htmlspecialchars($post['caption']); ?>">
                    </div>
                    <?php endforeach; ?>

                    <?php if (empty($postagens)): ?>
                        <p>Este usuário ainda não postou nada.</p>
                    <?php endif; ?>

                </div>
            </section>
        </main>

        <aside class="sidebar-right">
            <div class="user-tools">
                <div class="search-bar">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" placeholder="Buscar...">
                </div>
                <div class="tool-icons">
                    <a href="calendario.php"><i class="fa-solid fa-calendar-days"></i></a>
                    <a href="perfil.php"><i class="fa-solid fa-user"></i></a>
                </div>
            </div>
        </aside>
    </div>

    <div class="modal fade" id="viewPostModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Visualizar Post</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <img src="" id="modal-post-image" style="width: 100%; border-radius: 8px;" alt="Post">
                    <p id="modal-post-caption" style="margin-top: 15px; font-size: 18px;"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var viewPostModal = document.getElementById('viewPostModal');

    // Apenas mostra a foto e a legenda, nada de exclusão aqui
    viewPostModal.addEventListener('show.bs.modal', function(event) {
        var item = event.relatedTarget; 
        var imgSrc = item.dataset.imgSrc;
        var caption = item.dataset.caption;

        var modalImage = viewPostModal.querySelector('#modal-post-image');
        var modalCaption = viewPostModal.querySelector('#modal-post-caption');
        
        modalImage.src = imgSrc;
        if (caption) {
            modalCaption.textContent = caption;
            modalCaption.style.display = 'block';
        } else {
            modalCaption.style.display = 'none';
        }
    });
});
</script>

</body>
</html>