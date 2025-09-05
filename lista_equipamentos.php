<?php
// Inclui a configuração do banco de dados
require_once 'db_config.php';

// Mapeamento de status para mensagens de feedback
$status_messages = [
    'sucesso' => ['text' => 'Equipamento atualizado com sucesso!', 'class' => 'success'],
    'excluido_sucesso' => ['text' => 'Equipamento excluído com sucesso!', 'class' => 'success'],
    'erro' => ['text' => 'Ocorreu um erro. Tente novamente.', 'class' => 'error'],
];
$status = isset($_GET['status']) && isset($status_messages[$_GET['status']]) ? $status_messages[$_GET['status']] : null;

// Busca todos os equipamentos
$sql = "SELECT id, nome, setor, descricao, foto FROM equipamentos ORDER BY id DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Equipamentos</title>
    <link href="https://fonts.googleapis.com/css?family=Roboto:400,700&display=swap" rel="stylesheet">
    <link href="style.css" rel="stylesheet">
    <style>
        /* Estilos para as mensagens de feedback */
        .feedback-message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            color: #fff;
            text-align: center;
            font-weight: 500;
            opacity: 1;
            transition: opacity 0.5s ease-out;
        }
        .feedback-message.success {
            background-color: #28a745; /* Verde */
        }
        .feedback-message.error {
            background-color: #dc3545; /* Vermelho */
        }
        .feedback-message.hidden {
            opacity: 0;
        }
        /* Estilos adicionais para a página de listagem */
        .table-container {
            overflow-x: auto; /* Garante que a tabela seja rolável em telas pequenas */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #b3c7e6;
            padding: 12px 15px;
            text-align: left;
            color: #2d3e5e;
        }
        thead {
            background-color: #eaf2fb;
        }
        th {
            font-weight: 700;
        }
        tbody tr:nth-child(even) {
            background-color: #f8fbff;
        }
        .equip-thumb {
            max-width: 80px;
            height: auto;
            border-radius: 8px;
        }
        .actions a {
            text-decoration: none;
            color: #4f5efc;
            margin-right: 10px;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Lista de Equipamentos</h1>

        <?php if ($status): ?>
            <div id="feedback" class="feedback-message <?php echo $status['class']; ?>"><?php echo $status['text']; ?></div>
        <?php endif; ?>

        <a href="index.html" class="submit-btn back-link">Cadastrar Novo Equipamento</a>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Setor</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><img src="uploads/<?php echo htmlspecialchars($row['foto']); ?>" alt="<?php echo htmlspecialchars($row['nome']); ?>" class="equip-thumb"></td>
                                <td><?php echo htmlspecialchars($row['nome']); ?></td>
                                <td><?php echo htmlspecialchars($row['setor']); ?></td>
                                <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                                <td class="actions">
                                    <a href="editar_equipamento.php?id=<?php echo $row['id']; ?>" class="action-btn edit-btn">Editar</a>
                                    <a href="excluir_equipamento.php?id=<?php echo $row['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Tem certeza que deseja excluir este equipamento?');">Excluir</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center;">Nenhum equipamento cadastrado.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        // Script para fazer a mensagem de feedback desaparecer suavemente
        document.addEventListener('DOMContentLoaded', function() {
            const feedback = document.getElementById('feedback');
            if (feedback) {
                setTimeout(() => {
                    feedback.classList.add('hidden');
                }, 3000); // A mensagem some após 3 segundos
            }
        });
    </script>
</body>
</html>
<?php
$conn->close();
?>
