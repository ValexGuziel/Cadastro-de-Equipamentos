<?php
/**
 * Configuração e Conexão com o Banco de Dados
 *
 * Este arquivo centraliza as credenciais do banco de dados e cria a conexão,
 * evitando repetição de código e facilitando a manutenção.
 */

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "industria";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conexão com o banco de dados: " . $conn->connect_error);
}