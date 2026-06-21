<?php 
include 'conexao.php';
include 'includes/cabecalho.php';

// ==============================================================================
// CRITÉRIOS TECH FORGE (Modularização, Parâmetros/Retorno, Filtro e Validação)
// ==============================================================================

// Função 1: Processamento e Validação de Regras de Negócio
function calcularValorAVista($preco) {
    // Validação com Condicionais: Verifica se o preço não é zero ou negativo
    if (empty($preco) || $preco <= 0) {
        return "Valor sob consulta";
    }
    
    // Regra de Negócio: 10% de desconto para pagamento à vista
    $desconto = $preco * 0.10;
    $precoFinal = $preco - $desconto;
    
    // Fluxo de Dados: Retorna o resultado em vez de usar variável global
    return "R$ " . number_format($precoFinal, 2, ',', '.');
}

// Função 2: Lógica de Pesquisa ou Filtro dentro do Array
function filtrarServicosPorBusca($arrayServicos, $termoBusca) {
    if (empty($termoBusca)) {
        return $arrayServicos; // Retorna tudo se não houver busca
    }

    $arrayFiltrado = [];
    foreach ($arrayServicos as $servico) {
        // Valida se o termo de busca existe no nome do serviço
        if (stripos($servico['nome'], $termoBusca) !== false) {
            $arrayFiltrado[] = $servico;
        }
    }
    return $arrayFiltrado;
}
// ==============================================================================

// Busca os dados no Banco de Dados
try {
    $sql = "SELECT s.*, GROUP_CONCAT(c.nome SEPARATOR ', ') AS categorias_nomes
            FROM servicos s
            LEFT JOIN servico_categoria sc ON s.id = sc.servico_id
            LEFT JOIN categorias c ON sc.categoria_id = c.id
            GROUP BY s.id";
            
    $stmt = $conexao->prepare($sql);
    $stmt->execute();
    
    // ARMAZENAMENTO ESTRUTURADO: Todos os dados em um único array
   $resultado_banco = $stmt->get_result();
   $listaServicos = $resultado_banco->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    echo "<div class='alert alert-danger'>Erro: " . $e->getMessage() . "</div>";
    $listaServicos = []; 
}

// Aplicando o FILTRO da Tech Forge (Exemplo: se o usuário quisesse buscar apenas "Terapia")
// Você pode mudar esse valor ou deixá-lo vazio "" para mostrar todos.
$termoPesquisa = ""; 
$listaServicos = filtrarServicosPorBusca($listaServicos, $termoPesquisa);

?>

<div class="row text-center my-4" id="servicos">
    <div class="col-12">
        <h2 class="fw-bold text-secondary">Nossos Serviços e Especialidades</h2>
        <p class="text-muted">Clique em um serviço para saber mais ou agendar.</p>
    </div>
</div>

<div class="row row-cols-1 row-cols-md-3 g-4">
    
    <?php if (!empty($listaServicos)): 
        foreach ($listaServicos as $servico): 
    ?>
        <div class="col">
            <div class="card h-100 shadow-sm border-0" style="background-color: #2b2b2b;">
                <div class="card-body d-flex flex-column">
                    
                    <div class="mb-3">
                        <?php 
                        // O explode separa o texto gigante em um array de palavras curtas
                        $categorias = explode(', ', $servico['categorias_nomes'] ?? 'Geral');
                        foreach ($categorias as $categoria): 
                        ?>
                            <span class="badge bg-info text-dark text-uppercase text-wrap me-1 mb-1" style="font-size: 0.70rem;">
                                <?php echo htmlspecialchars($categoria); ?>
                            </span>
                        <?php endforeach; ?>
                    </div>

                    <h5 class="card-title fw-bold text-white mt-1">
                        <?php echo htmlspecialchars($servico['nome']); ?>
                    </h5>
                    <p class="card-text text-light flex-grow-1" style="opacity: 0.8;">
                        <?php echo htmlspecialchars($servico['descricao']); ?>
                    </p>
                    
                    <hr class="text-secondary">
                    
                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <small class="text-light" style="opacity: 0.7;">
                            ⏱️ <?php echo $servico['duracao_minutos']; ?> min
                        </small>
                        <div class="text-end">
                            <span class="text-success fw-bold d-block fs-5">
                                R$ <?php echo number_format($servico['preco'], 2, ',', '.'); ?>
                            </span>
                            <small class="text-info d-block" style="font-size: 0.75rem;">
                                À vista: <?php echo calcularValorAVista($servico['preco']); ?>
                            </small>
                        </div>
                    </div>
                    
                    <a href="#contato" class="btn btn-outline-info mt-3 w-100">Consultar Disponibilidade</a>
                </div>
            </div>
        </div>
    <?php 
        endforeach; 
    else: 
    ?>
        <div class="col-12 text-center">
            <p class="alert alert-warning">Nenhum serviço disponível no momento.</p>
        </div>
    <?php endif; ?>

</div>

<?php include 'includes/rodape.php'; ?>