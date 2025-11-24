<?php
//Inicia a sessão e verifica o login
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: login.html");
    exit;
}

//Conecta ao banco para descobrir o OBJETIVO do usuário
$servername = "localhost"; $username_db = "root"; $password_db = ""; $dbname = "mydb";
$conn = new mysqli($servername, $username_db, $password_db, $dbname);
if ($conn->connect_error) { die("Falha na conexão: " . $conn->connect_error); }

$id_usuario = $_SESSION['id'];
$sql = "SELECT objetivo FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$objetivo_usuario = $user['objetivo'] ?? 'Manter peso';

$conn->close();

//BANCO DE DADOS DE DICAS (Array PHP)
$todas_dicas = [
    'Perder peso' => [
        ['semana' => 1, 'cor' => 'green', 'nutri' => 'Nutricionista Ana', 'texto' => 'Comece bebendo pelo menos 2 litros de água por dia. A hidratação ajuda a controlar a fome e acelera o metabolismo.'],
        ['semana' => 1, 'cor' => 'green', 'nutri' => 'Nutricionista Murilo', 'texto' => 'Aumente o consumo de fibras (aveia, linhaça, vegetais). Elas aumentam a saciedade e ajudam o intestino.'],
        ['semana' => 2, 'cor' => 'red', 'nutri' => 'Nutricionista Bia', 'texto' => 'Evite alimentos ultraprocessados e ricos em açúcar refinado. Prefira frutas como sobremesa.'],
        ['semana' => 3, 'cor' => 'green', 'nutri' => 'Nutricionista Dani', 'texto' => 'Tente fazer o "prato ideal": 50% salada/legumes, 25% proteína e 25% carboidrato complexo.']
    ],
    'Ganhar peso' => [ //Ganho de massa
        ['semana' => 1, 'cor' => 'green', 'nutri' => 'Nutricionista Pedro', 'texto' => 'Não pule refeições! Tente comer a cada 3 horas para manter um fluxo constante de nutrientes.'],
        ['semana' => 1, 'cor' => 'green', 'nutri' => 'Nutricionista Ana', 'texto' => 'Aumente a ingestão de proteínas (ovos, frango, peixe, feijão) em todas as refeições principais.'],
        ['semana' => 2, 'cor' => 'red', 'nutri' => 'Nutricionista Murilo', 'texto' => 'Cuidado com o excesso de cardio. Foque em treinos de força e musculação para construir massa.'],
        ['semana' => 3, 'cor' => 'green', 'nutri' => 'Nutricionista Bia', 'texto' => 'Adicione gorduras boas (abacate, azeite, castanhas) para aumentar as calorias de forma saudável.']
    ],
    'Manter peso' => [
        ['semana' => 1, 'cor' => 'green', 'nutri' => 'Nutricionista Dani', 'texto' => 'Mantenha uma rotina alimentar equilibrada. A constância é a chave para manter seus resultados.'],
        ['semana' => 1, 'cor' => 'green', 'nutri' => 'Nutricionista Pedro', 'texto' => 'Varie as cores no seu prato. Quanto mais colorido, mais micronutrientes você está ingerindo.'],
        ['semana' => 2, 'cor' => 'red', 'nutri' => 'Nutricionista Ana', 'texto' => 'Atenção aos finais de semana. Tente manter o equilíbrio e não exagerar demais nas "refeições livres".'],
        ['semana' => 3, 'cor' => 'green', 'nutri' => 'Nutricionista Murilo', 'texto' => 'Continue praticando exercícios físicos regularmente, pelo menos 30 minutos por dia.']
    ]
];

//Seleciona as dicas com base no objetivo (ou usa 'Manter peso' se não encontrar)
$dicas_exibir = $todas_dicas[$objetivo_usuario] ?? $todas_dicas['Manter peso'];

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dicas Nutricionais</title>

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
            font-size: 20px; 
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
        .tool-icons a:hover { 
            color: #86A754; 
        }
        .main-content { 
            background-color: #fff; 
            padding: 25px; 
            border-radius: 12px; 
        }
        .main-content h1 { 
            font-family: 'Louis George Cafe', sans-serif; 
            color: #F8694D; 
            text-align: center; 
            font-size: 35px; 
            font-weight: bold; 
            margin-bottom: 30px; 
        }
        .main-content h4 { 
            text-align: center; 
            color: #777; 
            margin-bottom: 30px; 
            font-size: 18px; 
        } /* Subtítulo */
        .main-content h3 { 
            font-family: 'Louis George Cafe', sans-serif; 
            font-weight: bold; 
            font-size: 23px; 
            color: #333; 
            margin-top: 25px; 
            margin-bottom: 15px; 
        }
        .tip-card { 
            display: flex; 
            align-items: flex-start; 
            padding: 20px; 
            border-radius: 12px; 
            margin-bottom: 15px; 
        }
        .tip-card-green { 
            background-color: #C8E6C9; 
        }
        .tip-card-red { 
            background-color: #FADCD4; 
        }
        .tip-icon { 
            flex-shrink: 0; 
            width: 20px; 
            height: 20px; 
            background-color: #f0f0f0; 
            border: 1px solid #ccc; 
            border-radius: 50%; 
            margin-right: 15px; 
            margin-top: 5px; 
        }
        .tip-content strong { 
            display: block; 
            font-family: 'Louis George Cafe', sans-serif; 
            font-weight: 700; 
            font-size: 19px; 
            color: #222; 
            margin-bottom: 5px; 
        }
        .tip-content p { 
            font-family: 'Louis George Cafe', sans-serif; 
            font-size: 17px; 
            color: #555; 
            line-height: 1.4; 
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
                <li><a href="refeicao.php"><i class="fa-solid fa-utensils"></i> <span>Refeições</span></a></li>
                <li><a href="dicas.php" class="active"><i class="fa-solid fa-pencil"></i> <span>Dica do Nutri</span></a></li>
                <li><a href="progresso.php"><i class="fa-solid fa-chart-line"></i> <span>Progresso</span></a></li>
            </ul>
        </nav>

        <main class="main-content">
            <h1>Dicas Nutricionais</h1>
            <h4>Foco: <?php echo htmlspecialchars($objetivo_usuario); ?></h4>

            <section class="week-section">
                <h3>Semana 1</h3>
                <?php foreach ($dicas_exibir as $dica): ?>
                    <?php if ($dica['semana'] == 1): ?>
                        <div class="tip-card tip-card-<?php echo $dica['cor']; ?>">
                            <div class="tip-content">
                                <strong><?php echo $dica['nutri']; ?></strong>
                                <p><?php echo $dica['texto']; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="week-section">
                <h3>Semana 2</h3>
                <?php foreach ($dicas_exibir as $dica): ?>
                    <?php if ($dica['semana'] == 2): ?>
                        <div class="tip-card tip-card-<?php echo $dica['cor']; ?>">
                            <div class="tip-content">
                                <strong><?php echo $dica['nutri']; ?></strong>
                                <p><?php echo $dica['texto']; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            </section>

            <section class="week-section">
                <h3>Semana 3</h3>
                <?php foreach ($dicas_exibir as $dica): ?>
                    <?php if ($dica['semana'] == 3): ?>
                        <div class="tip-card tip-card-<?php echo $dica['cor']; ?>">
                            <div class="tip-content">
                                <strong><?php echo $dica['nutri']; ?></strong>
                                <p><?php echo $dica['texto']; ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
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
                    <a href="perfil.php" title="Perfil"><i class="fa-solid fa-user"></i></a>
                </div>
            </div>
        </aside>
    </div>

</body>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</html>