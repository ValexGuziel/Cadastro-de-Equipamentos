<?php
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    require_once 'db_config.php'; // Usa a configuração centralizada

    // Recebe dados do formulário
    $id = $_POST['id'];
    $nome = $_POST['nome'];
    $setor = $_POST['setor'];
    $descricao = $_POST['descricao'];
    $foto_antiga = $_POST['foto_antiga'];
    $foto_nome = $foto_antiga; // Assume que a foto não mudou

    // Validação básica
    if (empty($id) || empty($nome) || empty($setor)) {
        die("Erro: ID, Nome e Setor são campos obrigatórios.");
    }

    // 1. Lógica para upload de nova foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        // Gera um nome de arquivo único para evitar conflitos
        $foto_nome = uniqid() . "_" . basename($_FILES["foto"]["name"]);
        $foto_destino = "uploads/" . $foto_nome;

        // Tenta mover o novo arquivo para a pasta de uploads
        if (move_uploaded_file($_FILES["foto"]["tmp_name"], $foto_destino)) {
            // Se o upload foi bem-sucedido, remove a foto antiga (se existir)
            if (!empty($foto_antiga) && file_exists("uploads/" . $foto_antiga)) {
                unlink("uploads/" . $foto_antiga);
            }
        } else {
            // Se o upload falhar, mantém a foto antiga e informa um erro
            $foto_nome = $foto_antiga;
            echo "Aviso: Houve um erro no upload da nova imagem. A imagem antiga foi mantida.";
        }
    }

    // 2. Atualiza o registro no banco de dados
    $sql = "UPDATE equipamentos SET nome = ?, setor = ?, descricao = ?, foto = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        die("Erro ao preparar a query: " . $conn->error);
    }

    $stmt->bind_param("ssssi", $nome, $setor, $descricao, $foto_nome, $id);

    if ($stmt->execute()) {
        // Redireciona para a lista com uma mensagem de sucesso (opcional)
        header("Location: lista_equipamentos.php?status=sucesso#feedback");
        exit();
    } else {
        echo "Erro ao atualizar o equipamento: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    // Se o script for acessado diretamente, redireciona para a lista
    header("Location: lista_equipamentos.php");
    exit();
}
?>
