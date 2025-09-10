<?php
$current_page = basename($_SERVER['SCRIPT_NAME']);
?>
<nav class="main-nav">
    <a href="index.html" class="<?php echo ($current_page == 'index.php' || $current_page == 'index.html') ? 'active' : ''; ?>"><i class="fa-solid fa-plus"></i> Cadastrar Equipamento</a>
    <a href="listar_equipamentos.php" class="<?php echo ($current_page == 'listar_equipamentos.php') ? 'active' : ''; ?>"><i class="fa-solid fa-list"></i>Equipamentos Cadastrados</a>
    <a href="relatorios.php" class="<?php echo ($current_page == 'relatorios.php') ? 'active' : ''; ?>"><i class="fa-solid fa-chart-pie"></i> Relatório de Gastos</a>
</nav>