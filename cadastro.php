<?php
// Define o cabeçalho da resposta como texto simples com codificação UTF-8
header('Content-Type: text/plain; charset=utf-8');

// Inclui a configuração do banco de dados
require_once 'db_config.php';

// --- 1. Definição do diretório de upload ---
// Garante que o caminho do diretório seja relativo ao script atual
$uploadDir = __DIR__ . '/uploads/';

// Cria o diretório 'uploads' se ele não existir
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0777, true)) {
        // Se não for possível criar o diretório, encerra com erro
        http_response_code(500); // Erro interno do servidor
        die('Erro: Não foi possível criar o diretório de uploads.');
    }
}

// --- 2. Validação dos dados recebidos via POST ---
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405); // Método não permitido
    die('Erro: Este script aceita apenas requisições POST.');
}

// Verifica se os campos de texto esperados foram enviados
$nome = isset($_POST['nome']) ? trim($_POST['nome']) : '';
$setor = isset($_POST['setor']) ? trim($_POST['setor']) : '';
$descricao = isset($_POST['descricao']) ? trim($_POST['descricao']) : '';

if (empty($nome) || empty($setor)) {
    http_response_code(400); // Requisição inválida
    die('Erro: Os campos "Nome do Equipamento" e "Setor" são obrigatórios.');
}

$foto_nome_final = null; // Inicializa a variável do nome da foto

// --- 3. Processamento do Upload da Imagem ---
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $fotoTmpPath = $_FILES['foto']['tmp_name'];
    $fotoName = basename($_FILES['foto']['name']);
    // Cria um nome de arquivo único para evitar sobrescrever arquivos existentes
    $foto_nome_final = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9\.\-]/", "_", $fotoName);
    $fotoDestPath = $uploadDir . $foto_nome_final;

    // Move o arquivo temporário para o diretório de destino
    if (!move_uploaded_file($fotoTmpPath, $fotoDestPath)) {
        http_response_code(500);
        die('Erro ao salvar a imagem.');
    }
} else {
    http_response_code(400);
    die('Erro: A foto do equipamento é obrigatória.');
}

// --- 4. Inserção dos dados no Banco de Dados ---
$sql = "INSERT INTO equipamentos (nome, setor, descricao, foto) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    http_response_code(500);
    die("Erro ao preparar a query: " . $conn->error);
}

$stmt->bind_param("ssss", $nome, $setor, $descricao, $foto_nome_final);

if ($stmt->execute()) {
    // --- 5. Resposta de Sucesso ---
    echo 'Equipamento cadastrado com sucesso!';
} else {
    http_response_code(500);
    // Em um ambiente de produção, você poderia logar o erro em vez de exibi-lo.
    die("Erro ao cadastrar o equipamento: " . $stmt->error);
}

$stmt->close();
$conn->close();
?>