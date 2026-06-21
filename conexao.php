<?php
$host = '192.168.1.24'; 
$usuario = 'gabriel';    
$senha = '1234';       
$banco = 'catalogo_psicologo'; 

$conexao = new mysqli($host, $usuario, $senha, $banco);

if ($conexao->connect_error) {
    die("Falha na conexão: " . $conexao->connect_error);
}
?>