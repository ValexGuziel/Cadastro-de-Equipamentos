<?php
require_once 'db_config.php'; // Usa a configuração centralizada

// --- 1. Validação do ID ---
// Verifica se o ID foi passado pela URL e se é um número inteiro.
// Isso é uma medida de segurança crucial contra SQL Injection.
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    // Se o ID for inválido, redireciona para a lista sem fazer nada.
    header("Location: lista_equipamentos.php?status=erro_id");
    exit();
}

$id = $_GET['id'];

// --- 3. Busca o nome do arquivo da foto antes de excluir o registro ---
// Precisamos do nome do arquivo para poder excluí-lo da pasta 'uploads'.
$sql_select_foto = "SELECT foto FROM equipamentos WHERE id = ?";
$stmt_select = $conn->prepare($sql_select_foto);

if ($stmt_select) {
    $stmt_select->bind_param("i", $id);
    $stmt_select->execute();
    $result = $stmt_select->get_result();
    
    if ($row = $result->fetch_assoc()) {
        $foto_path = __DIR__ . '/uploads/' . $row['foto'];
        // Se o arquivo da foto existir, exclui-o.
        if (!empty($row['foto']) && file_exists($foto_path)) {
            unlink($foto_path);
        }
    }
    $stmt_select->close();
}

// --- 4. Exclusão do Registro do Banco de Dados ---
// Usamos um 'prepared statement' para excluir o equipamento com segurança.
$sql_delete = "DELETE FROM equipamentos WHERE id = ?";
$stmt_delete = $conn->prepare($sql_delete);

if ($stmt_delete === false) {
    die("Erro ao preparar a query de exclusão: " . $conn->error);
}

$stmt_delete->bind_param("i", $id);

if ($stmt_delete->execute()) {
    // Se a exclusão for bem-sucedida, redireciona para a lista de equipamentos.
    header("Location: lista_equipamentos.php?status=excluido_sucesso#feedback");
} else {
    // Se houver um erro, redireciona com uma mensagem de erro.
    header("Location: lista_equipamentos.php?status=erro#feedback");
}

$stmt_delete->close();
$conn->close();
exit(); // Garante que o script pare aqui.
?>