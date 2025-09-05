<?php
require_once 'db_config.php'; // Usa a configuração centralizada

// 1. Validação do ID
// Verifica se o ID foi passado pela URL e se é um número válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: lista_equipamentos.php"); // Redireciona se o ID for inválido
    exit();
}

$id = $_GET['id'];

// 2. Busca os dados do equipamento específico
$sql = "SELECT nome, setor, descricao, foto FROM equipamentos WHERE id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    die("Erro ao preparar a query: " . $conn->error);
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$equipamento = $result->fetch_assoc();

// Se nenhum equipamento for encontrado com o ID, redireciona para a lista
if (!$equipamento) {
    header("Location: lista_equipamentos.php");
    exit();
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Equipamento</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Editar Equipamento</h1>
        <a href="lista_equipamentos.php" class="submit-btn" style="text-decoration: none; margin-bottom: 20px; display: inline-block;">Voltar para a Lista</a>

        <!-- O formulário envia os dados para 'atualizar_equipamento.php' -->
        <form action="atualizar_equipamento.php" method="POST" enctype="multipart/form-data">

            <!-- Campo oculto para enviar o ID do equipamento -->
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <!-- Campo oculto para guardar o nome da foto atual -->
            <input type="hidden" name="foto_antiga" value="<?php echo htmlspecialchars($equipamento['foto']); ?>">

            <div class="form-row">
                <label for="nome">Nome do Equipamento:</label>
                <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($equipamento['nome']); ?>" required>
            </div>
            <div class="form-row">
                <label for="setor">Setor:</label>
                <input type="text" id="setor" name="setor" value="<?php echo htmlspecialchars($equipamento['setor']); ?>" required>
            </div>
            <div class="form-row">
                <label for="descricao">Descrição:</label>
                <input type="text" id="descricao" name="descricao" value="<?php echo htmlspecialchars($equipamento['descricao']); ?>">
            </div>
            <div class="form-row form-row-photo">
                <div>
                    <label for="foto">Alterar Foto (opcional):</label>
                    <input type="file" id="foto" name="foto" accept="image/*">
                    <p style="font-size: 12px; color: #666; margin-top: 5px;">Deixe em branco para manter a imagem atual.</p>
                </div>
                <div>
                    <p>Foto Atual:</p>
                    <img src="uploads/<?php echo htmlspecialchars($equipamento['foto']); ?>" alt="Foto atual" class="equip-photo" style="display: block;">
                </div>
            </div>

            <button type="submit" class="submit-btn">Salvar Alterações</button>
        </form>
    </div>

    <script>
        // Script simples para pré-visualização da nova imagem (opcional, mas melhora a UX)
        document.getElementById('foto').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Encontra a imagem de pré-visualização e atualiza o 'src'
                    const previewImg = document.querySelector('.equip-photo');
                    previewImg.src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
