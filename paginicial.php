<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

$id_usuario = $_SESSION['id'];

// Busca Feed Global
$sql_feed = "SELECT 
                p.id as post_id,
                p.imagem_path, 
                p.caption, 
                p.data_postagem, 
                u.id as user_id,
                u.username, 
                u.profile_pic 
             FROM postagens p
             JOIN usuarios u ON p.id_usuario = u.id
             ORDER BY p.data_postagem DESC";

$result_feed = $conn->query($sql_feed);
$feed_posts = [];
if ($result_feed) {
    while ($row = $result_feed->fetch_assoc()) {
        $feed_posts[] = $row;
    }
}
$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Inicial</title>
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
        height: 95vh; 
        top: 20px; 
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
    .feed { 
        max-width: 680px; 
    }
    .carousel img { 
        width: 100%; 
        border-radius: 12px;
        margin-bottom: 20px; 
    }
    .post-card { 
        background-color: white; 
        border-radius: 12px; 
        margin-bottom: 20px; 
        border: 1px solid #eee; 
        overflow: hidden; 
    }
    .post-header { 
        display: flex; 
        align-items: center; 
        padding: 15px; 
    }
    
    /* Avatar com borda */
    .user-avatar { 
        width: 40px; 
        height: 40px; 
        border-radius: 50%; 
        margin-right: 10px; 
        object-fit: cover; 
        border: 2px solid #F8694D; 
    }
    
    .profile-link { 
        text-decoration: none; 
        color: inherit; 
        display: flex; 
        align-items: center; 
    }
    .profile-link:hover .username { 
        color: #F8694D; 
    }
    .username { 
        font-weight: bold; 
        font-size: 18px; 
        color: #333; 
    }
    .post-image { 
        width: 100%; 
        height: auto; 
        display: block; 
    }
    .post-caption { 
        padding: 15px 15px 5px 15px; 
        font-size: 16px; 
        line-height: 1.5; 
        color: #555; 
    }
    .post-caption strong { 
        color: #333; 
        margin-right: 5px; 
    }
    
    .comments-section { 
        padding: 0 15px 15px 15px; 
        border-top: 1px solid #f0f0f0; 
        background-color: #fafafa; 
    }
    .comment-list { 
        list-style: none; 
        padding: 0; 
        margin: 10px 0; 
        font-size: 15px; 
        max-height: 200px; 
        overflow-y: auto; 
    }
    .comment-item { 
        margin-bottom: 8px; 
        font-size: 15px; 
        border-bottom: 1px solid #eee; 
        padding-bottom: 4px; display: flex; 
        justify-content: space-between; 
        align-items: center; 
    }
    .comment-text { 
        flex-grow: 1; 
    }
    .comment-item strong { 
        color: #F8694D;
        margin-right: 5px; font-size: 14px; }
    .delete-comment-btn { color: #dc3545; cursor: pointer; font-size: 12px; margin-left: 10px; opacity: 0.7; }
    .delete-comment-btn:hover { opacity: 1; }
    
    .comment-form { display: flex; gap: 10px; margin-top: 10px; }
    .comment-input { flex-grow: 1; border: 1px solid #ddd; border-radius: 20px; padding: 8px 15px; outline: none; font-size: 14px; }
    .btn-comment { background-color: #F8694D; color: white; border: none; border-radius: 20px; padding: 5px 20px; font-size: 14px; cursor: pointer; font-weight: bold; }
    .btn-comment:hover { background-color: #e05a3f; }

    .sidebar-right { position: sticky; top: 20px; }
    .user-tools { background-color: #C8E6C9; padding: 15px; border-radius: 12px; width: 400px; }
    .search-bar { display: flex; align-items: center; background-color: white; padding: 8px; border-radius: 20px; margin-bottom: 20px; }
    .search-bar input { border: none; outline: none; background: none; width: 100%; margin-left: 8px; }
    .tool-icons { display: flex; justify-content: space-around; }
    .tool-icons a { font-size: 22px; color: #333; }
    .tool-icons a.active { color: #F8694D; }
    </style>
</head>
<body>

    <div class="container">
        <nav class="sidebar-left">
            <div class="logo"><img src="img/logo.png" alt="FitTech Logo"></div>
            <ul>
                <li><a href="paginicial.php" class="active"><i class="fa-solid fa-house"></i> <span>Página Inicial</span></a></li>
                <li><a href="refeicao.php"><i class="fa-solid fa-utensils"></i> <span>Refeições</span></a></li>
                <li><a href="dicas.php"><i class="fa-solid fa-pencil"></i> <span>Dica do Nutri</span></a></li>
                <li><a href="progresso.php"><i class="fa-solid fa-chart-line"></i> <span>Progresso</span></a></li>
            </ul>
        </nav>

        <main class="feed">
            <div id="carouselExampleIndicators" class="carousel slide" data-bs-ride="carousel">
                 <div class="carousel-indicators">
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="0" class="active"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="1"></button>
                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="2"></button>
                </div>
                <div class="carousel-inner">
                    <div class="carousel-item active"><img src="img/darklab.png" class="d-block w-100" alt="..."></div>
                    <div class="carousel-item"><img src="img/growth.png" class="d-block w-100" alt="..."></div>
                    <div class="carousel-item"><img src="img/max.png" class="d-block w-100" alt="..."></div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
            </div>

            <?php foreach ($feed_posts as $post): ?>
            <article class="post-card" data-post-id="<?php echo $post['post_id']; ?>">
                <div class="post-header">
                    <?php $profileLink = ($post['user_id'] == $_SESSION['id']) ? 'perfil.php' : 'perfil_usuario.php?id=' . $post['user_id']; ?>
                    <a href="<?php echo $profileLink; ?>" class="profile-link">
                        
                        <img src="<?php echo htmlspecialchars($post['profile_pic']); ?>" 
                             alt="Foto" 
                             class="user-avatar"
                             onerror="this.src='https://cdn-icons-png.flaticon.com/512/847/847969.png'">
                             
                        <span class="username"><?php echo htmlspecialchars($post['username']); ?></span>
                    </a>
                </div>
                
                <img class="post-image" src="<?php echo htmlspecialchars($post['imagem_path']); ?>" alt="Post">
                
                <div class="post-caption">
                    <p>
                        <strong><?php echo htmlspecialchars($post['username']); ?></strong> 
                        <?php echo htmlspecialchars($post['caption']); ?>
                    </p>
                </div>

                <div class="comments-section">
                    <ul class="comment-list" id="comments-list-<?php echo $post['post_id']; ?>"></ul>
                    
                    <div class="comment-form">
                        <input type="text" 
                               class="comment-input" 
                               id="input-<?php echo $post['post_id']; ?>" 
                               placeholder="Adicione um comentário..."
                               onkeypress="checarEnter(event, <?php echo $post['post_id']; ?>)">
                        
                        <button class="btn-comment" onclick="enviarComentario(<?php echo $post['post_id']; ?>)">Publicar</button>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>

            <?php if (empty($feed_posts)): ?>
                <p style="text-align: center; color: #777; margin-top: 20px;">Ainda não há publicações. Vá ao perfil e adicione a primeira!</p>
            <?php endif; ?>

        </main>

        <aside class="sidebar-right">
            <div class="user-tools">
                
                <div class="search-bar" style="position: relative;">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="searchInput" placeholder="Buscar usuários..." autocomplete="off">
                    
                    <div id="searchResults" class="search-dropdown"></div>
                </div>

                <div class="tool-icons">
                    <a href="notificacao.php" title="Notificações"><i class="fa-solid fa-bell"></i></a>
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
    top: 100%; /* Logo abaixo do input */
    left: 0;
    width: 100%;
    background-color: white;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    z-index: 1000;
    margin-top: 5px;
    display: none; /* Começa invisível */
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

    // Escuta o que você digita
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
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ID do usuário logado
    const currentUserId = <?php echo $_SESSION['id']; ?>;

    async function carregarComentarios(postId) {
        try {
            const response = await fetch(`api_get_comments.php?id_post=${postId}`);
            const comentarios = await response.json();
            
            const lista = document.getElementById(`comments-list-${postId}`);
            lista.innerHTML = ''; 

            comentarios.forEach(c => {
                const li = document.createElement('li');
                li.className = 'comment-item';
                
                let html = `<span class="comment-text"><strong>${c.username}</strong> ${c.texto}</span>`;

                if (c.id_usuario == currentUserId) {
                    html += `<i class="fa-solid fa-trash delete-comment-btn" onclick="excluirComentario(${c.id}, ${postId})" title="Excluir"></i>`;
                }

                li.innerHTML = html;
                lista.appendChild(li);
            });
            lista.scrollTop = lista.scrollHeight;

        } catch (error) { console.error("Erro ao carregar comentários:", error); }
    }

    async function enviarComentario(postId) {
        const input = document.getElementById(`input-${postId}`);
        const texto = input.value;

        if (!texto.trim()) return;

        try {
            const response = await fetch('api_add_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_post: postId, texto: texto })
            });
            
            const result = await response.json();

            if (result.success) {
                input.value = ''; 
                carregarComentarios(postId); 
            } else {
                alert('Erro: ' + (result.error || 'Desconhecido'));
            }
        } catch (error) { console.error("Erro ao enviar:", error); }
    }

    async function excluirComentario(commentId, postId) {
        if (!confirm("Deseja apagar este comentário?")) return;

        try {
            const response = await fetch('api_delete_comment.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_comentario: commentId })
            });

            const result = await response.json();
            
            if (result.success) {
                carregarComentarios(postId); 
            } else {
                alert("Erro ao excluir: " + result.error);
            }

        } catch (error) { console.error("Erro ao excluir:", error); }
    }

    function checarEnter(event, postId) {
        if (event.key === "Enter") {
            enviarComentario(postId);
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.post-card').forEach(card => {
            const postId = card.dataset.postId;
            carregarComentarios(postId);
        });
    });
</script>
</html>