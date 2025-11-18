<?php
// 1. Inicia a sessão e verifica o login
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

// 2. Conecta ao banco
$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

$id_usuario = $_SESSION['id'];

// 3. Busca os dados do USUÁRIO (perfil)
$sql_user = "SELECT username, COALESCE(bio, '') as bio, profile_pic FROM usuarios WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $id_usuario);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

// 4. Busca os POSTS do feed 
$sql_posts = "SELECT id, imagem_path, caption FROM postagens WHERE id_usuario = ? ORDER BY data_postagem DESC";
$stmt_posts = $conn->prepare($sql_posts);
$stmt_posts->bind_param("i", $id_usuario);
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
    .tool-icons a.active {
        color: #F8694D; 
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
        font-size: 80px;
        color: #aaa;
        overflow: hidden;
    }
    /*imagem de perfil */
    .avatar-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .profile-avatar .add-icon {
        position: absolute;
        bottom: 5px;
        right: 5px;
        width: 35px;
        height: 35px;
        background-color: #333;
        color: white;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 16px;
        cursor: pointer;
        border: 2px solid #FFF9EA;
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
    .profile-actions {
        display: flex;
        gap: 15px;
        margin-top: 20px;
    }
    .profile-actions .btn-custom {
        background-color: #D4E0D0;
        border: none;
        padding: 10px 20px;
        border-radius: 10px;
        font-family: 'Louis George Cafe', sans-serif;
        font-size: 16px;
        font-weight: bold;
        color: #333;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .profile-actions .btn-custom:hover {
        background-color: #C8E6C9;
    }
    /*Header da galeria para o botão de adicionar */
    .diet-gallery-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    .diet-gallery h2 {
        font-size: 24px;
        font-weight: bold;
        color: #333;
        margin-bottom: 0; 
    }

    .btn-add-post {
        background-color: #F8694D;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 15px;
        font-weight: bold;
        font-size: 16px;
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
    .photo-grid > p {
        grid-column: 1 / -1; 
        text-align: left;
        font-size: 18px;
        color: #777;
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
                    <div class="add-icon" data-bs-toggle="modal" data-bs-target="#changePicModal">
                        <i class="fa-solid fa-plus"></i>
                    </div>
                </div>
                <div class="profile-info">
                    <h1 class="username"><?php echo htmlspecialchars($user['username']); ?></h1>
                    <p class="stats"><strong><?php echo $num_postagens; ?></strong> Publicações</p>
                    <p class="bio">
                        <?php echo htmlspecialchars($user['bio']) ?: 'Adicione uma bio clicando em "Editar perfil".'; ?>
                    </p>
                    <div class="profile-actions">
    <button class="btn-custom" data-bs-toggle="modal" data-bs-target="#editProfileModal">Editar perfil</button>
    <button class="btn-custom" style="background-color: #F8694D; color: white;" onclick="window.location.href='logout.php'">
        <i class="fa-solid fa-right-from-bracket"></i> Sair
    </button>
</div>
                </div>
            </header>

            <section class="diet-gallery">
                <div class="diet-gallery-header">
                    <h2>Minha dieta</h2>
                    <button class="btn-custom btn-add-post" data-bs-toggle="modal" data-bs-target="#addPostModal">
                        <i class="fa-solid fa-plus"></i> Adicionar
                    </button>
                </div>
                <div class="photo-grid">
                    
                    <?php foreach ($postagens as $post): ?>
                    <div class="photo-item" 
                        data-bs-toggle="modal" 
                        data-bs-target="#viewPostModal"
                        data-id="<?php echo $post['id']; ?>"
                        data-img-src="<?php echo htmlspecialchars($post['imagem_path']); ?>"
                        data-caption="<?php echo htmlspecialchars($post['caption']); ?>">
                        
                        <img src="<?php echo htmlspecialchars($post['imagem_path']); ?>" alt="<?php echo htmlspecialchars($post['caption']); ?>">
                    </div>
                    <?php endforeach; ?>

                    <?php if (empty($postagens)): ?>
                        <p style="max-width: 600px;">Adicione a primeira foto da sua dieta clicando em "Adicionar".</p>
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
                    <a href="calendario.php" title="Calendário"><i class="fa-solid fa-calendar-days"></i></a>
                    <a href="perfil.php" title="Perfil" class="active"><i class="fa-solid fa-user"></i></a>
                </div>
            </div>
        </aside>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="api_update_bio.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Perfil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="username" class="form-label">Nome de Usuário</label>
                            <input type="text" class="form-control" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="bio" class="form-label">Bio</label>
                            <textarea class="form-control" name="bio" rows="3" placeholder="Escreva um pouco sobre você..."><?php echo htmlspecialchars($user['bio']); ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #F8694D; border: none;">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="changePicModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="api_upload_avatar.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Alterar Foto de Perfil</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="avatarFile" class="form-label">Selecione uma imagem (jpg, png, jpeg):</label>
                            <input type="file" class="form-control" name="avatarFile" id="avatarFile" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #F8694D; border: none;">Salvar Foto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="addPostModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <form action="api_add_post.php" method="POST" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title">Adicionar Foto à Dieta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="postFile" class="form-label">Selecione a imagem:</label>
                            <input type="file" class="form-control" name="postFile" id="postFile" required>
                        </div>
                         <div class="mb-3">
                            <label for="caption" class="form-label">Legenda (opcional):</label>
                            <textarea class="form-control" name="caption" rows="2" placeholder="Ex: Meu almoço de hoje!"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #F8694D; border: none;">Publicar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="viewPostModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg"> <div class="modal-content">
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
                    <button type="button" class="btn btn-danger" id="deletePostButton">Excluir Post</button>
                </div>
            </div>
        </div>
    </div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var viewPostModal = document.getElementById('viewPostModal');
    var deleteButton = document.getElementById('deletePostButton');
    var currentPostId = null; // Variável para guardar o ID do post que está aberto

    // 1. Evento que abre o Modal (quando você clica em uma foto)
    viewPostModal.addEventListener('show.bs.modal', function(event) {
        // Pega o item que foi clicado
        var item = event.relatedTarget; 
        
        // Pega os dados que colocamos no HTML
        currentPostId = item.dataset.id; // Salva o ID do post
        var imgSrc = item.dataset.imgSrc;
        var caption = item.dataset.caption;

        // Coloca os dados dentro do modal
        var modalImage = viewPostModal.querySelector('#modal-post-image');
        var modalCaption = viewPostModal.querySelector('#modal-post-caption');
        
        modalImage.src = imgSrc;
        if (caption) {
            modalCaption.textContent = caption;
            modalCaption.style.display = 'block';
        } else {
            modalCaption.style.display = 'none'; // Esconde se não houver legenda
        }
    });

    // 2. Evento de clique no botão "Excluir Post"
    deleteButton.addEventListener('click', async function() {
        if (!currentPostId) return; // Não faz nada se não tiver um ID
        
        // Confirmação
        if (!confirm('Tem certeza que deseja excluir este post permanentemente?')) {
            return;
        }

        try {
            // Chama a nova API que criamos
            const response = await fetch('api_delete_post.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id: currentPostId })
            });

            if (!response.ok) {
                throw new Error('Falha na resposta da rede.');
            }

            const result = await response.json();

            if (result.success) {
                // Sucesso!
                // 1. Remove a foto da grade (sem recarregar a página)
                var itemParaRemover = document.querySelector(`.photo-item[data-id="${currentPostId}"]`);
                if (itemParaRemover) {
                    itemParaRemover.remove();
                }
                
                // 2. Fecha o modal
                var modalInstance = bootstrap.Modal.getInstance(viewPostModal);
                modalInstance.hide();
                
                // 3. (Opcional) Atualiza a contagem de posts (recarga é mais fácil)
                // Para simplificar, vamos apenas recarregar a página para atualizar a contagem
                location.reload(); 
                
            } else {
                throw new Error(result.error || 'Erro ao excluir.');
            }

        } catch (error) {
            console.error('Erro:', error);
            alert('Não foi possível excluir o post.');
        }
    });
});
</script>

</body>
</html>