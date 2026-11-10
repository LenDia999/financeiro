<?php
// index.php
$app = require dirname(__DIR__) . '/src/app.php';

use Slim\Factory\AppFactory;

$app = AppFactory::create();
$app->get('/status', function ($request, $response) {
    $response->getBody()->write(json_encode(['status' => 'ok']));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->addBodyParsingMiddleware();
$categorias = [
    ['id' => 1, 'nome' => 'Salário', 'tipo' => 'Receita'],
    ['id' => 2, 'nome' => 'Aluguel', 'tipo' => 'Despesa'],
    ['id' => 3, 'nome' => 'Compras', 'tipo' => 'Despesa'],
    ['id' => 4, 'nome' => 'Investimentos', 'tipo' => 'Receita'],
    ['id' => 5, 'nome' => 'ContasFixas', 'tipo' => 'Despesa']
];
$app->get('/categorias', function ($request, $response, $args) use (&$categorias) {
    // Escreve todo o array de categorias no corpo da resposta
    $response->getBody()->write(json_encode($categorias));


    // Retorna o JSON com o status HTTP 200 (Sucesso)
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->get('/categorias/{id}', function ($request, $response, $args) use (&$categorias) {
    $categoria = current(array_filter($categorias, fn($p) => $p['id'] === (int) $args['id'])) ?: null;
    if (!$categoria) {
        $response->getBody()->write(json_encode(['erro' => 'Categoria não encontrada']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
    $response->getBody()->write(json_encode($categoria));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->post('/categorias', function ($request, $response) use (&$categorias) {
    $dados = $request->getParsedBody();
    $novaCategoria = ['id' => count($categorias) + 1, 'nome' => $dados['nome'], 'tipo' => $dados['tipo']];
    $categorias[] = $novaCategoria;
    $response->getBody()->write(json_encode($novaCategoria));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});
$app->put('/categorias/{id}', function ($request, $response, $args) use (&$categorias) {
    $dados = $request->getParsedBody();
    foreach ($categorias as &$categoria) {
        if ($categoria['id'] === (int) $args['id']) {
            $categoria['nome'] = $dados['nome'];
            $categoria['tipo'] = $dados['tipo'];
            $response->getBody()->write(json_encode($categoria));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
    }
    return $response->withStatus(404);
});


$app->delete('/categorias/{id}', function ($request, $response, $args) use (&$categorias) {
    $categorias = array_values(array_filter($categorias, fn($p) => $p['id'] !== (int) $args['id']));
    return $response->withStatus(204);
});

$app->addBodyParsingMiddleware();
$lancamentos = [
    ['id' => 1, 'nome' => 'Novembro', 'tipo' => 'Receita', 'valor' => 10000, 'categorias' => 'Salário', 'data' => '2023-11-30'],
    ['id' => 2, 'nome' => 'Casa', 'tipo' => 'Despesa', 'valor' => 1000, 'categorias' => 'Aluguel', 'data' => '2023-12-01'],
    ['id' => 3, 'nome' => 'Burguer King', 'tipo' => 'Despesa', 'valor' => 90, 'categorias' => 'Compras', 'data' => '2023-12-02'],
    ['id' => 4, 'nome' => 'Criptomoedas', 'tipo' => 'Receita', 'valor' => 500, 'categorias' => 'Investimentos', 'data' => '2023-12-03'],
    ['id' => 5, 'nome' => 'Dezembro', 'tipo' => 'Receita', 'valor' => 12000, 'categorias' => 'Salário', 'data' => '2023-12-04'],
    ['id' => 6, 'nome' => 'Carro', 'tipo' => 'Despesa', 'valor' => 700, 'categorias' => 'ContasFixas', 'data' => '2023-12-05'],
    ['id' => 7, 'nome' => 'McDonalds', 'tipo' => 'Despesa', 'valor' => 80, 'categorias' => 'Compras', 'data' => '2023-12-06'],
    ['id' => 8, 'nome' => 'CDI', 'tipo' => 'Receita', 'valor' => 700, 'categorias' => 'Investimentos', 'data' => '2023-12-07']
];
$app->get('/lancamentos', function ($request, $response, $args) use (&$lancamentos) {
    // Escreve todo o array de lançamentos no corpo da resposta
    $response->getBody()->write(json_encode($lancamentos));


    // Retorna o JSON com o status HTTP 200 (Sucesso)
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->get('/lancamentos/{id}', function ($request, $response, $args) use (&$lancamentos) {
    $lancamento = current(array_filter($lancamentos, fn($p) => $p['id'] === (int) $args['id'])) ?: null;
    if (!$lancamento) {
        $response->getBody()->write(json_encode(['erro' => 'Lançamento não encontrado']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
    $response->getBody()->write(json_encode($lancamento));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->post('/lancamentos', function ($request, $response) use (&$lancamentos, $categorias) {
    $dados = $request->getParsedBody();
    $novoLancamento = ['id' => count($lancamentos) + 1, 'nome' => $dados['nome'], 'tipo' => $dados['tipo'], 'valor' => $dados['valor'], 'categorias' => $dados['id_categorias'], 'data' => $dados['data']];
    $lancamentos[] = $novoLancamento;
    $codigos_cateogira = array_column($categorias, 'id');

    if(!in_array($dados['id_categorias'], $codigos_cateogira)){
        $response->getBody()->write(json_encode([
            'erro' => 'Categoria não encontrada'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }
    
    $response->getBody()->write(json_encode($novoLancamento));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});
$app->put('/lancamentos/{id}', function ($request, $response, $args) use (&$lancamentos) {
    $dados = $request->getParsedBody();
    
    foreach ($lancamentos as $indice => $lancamento) {
        if ($lancamento['id'] === (int) $args['id']) {

            if (isset($dados['nome']) && !empty($dados['nome'])) {
                $lancamentos[$indice]['nome'] = $dados['nome'];
            }
            if (isset($dados['tipo']) && !empty($dados['tipo'])) {
                $lancamentos[$indice]['tipo'] = $dados['tipo'];
            }
            if (isset($dados['valor']) && !empty($dados['valor'])) {
                $lancamentos[$indice]['valor'] = $dados['valor'];
            }
            if (isset($dados['categorias']) && !empty($dados['categorias'])) {
                $lancamentos[$indice]['categorias'] = $dados['categorias'];
            }
            if (isset($dados['data']) && !empty($dados['data'])) {
                $lancamentos[$indice]['data'] = $dados['data'];
            }
            $response->getBody()->write(json_encode($lancamentos[$indice]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
        }
    }
    return $response->withStatus(404);
});


$app->delete('/lancamentos/{id}', function ($request, $response, $args) use (&$lancamentos) {
    $lancamentos = array_values(array_filter($lancamentos, fn($p) => $p['id'] !== (int) $args['id']));
    return $response->withStatus(204);
});
$app->run();
