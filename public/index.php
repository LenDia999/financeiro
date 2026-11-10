<?php
// index.php
require dirname(__DIR__) . '/vendor/autoload.php';
use Slim\Factory\AppFactory;
$app = AppFactory::create();
$app->get('/status', function ($request, $response) {
$response->getBody()->write(json_encode(['status' => 'ok']));
return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->addBodyParsingMiddleware();
$lancamentos = [
['id' => 1, 'nome' => 'Salário', 'tipo' => 'Receita', 'valor' => 5000],
['id' => 2, 'nome' => 'Aluguel', 'tipo' => 'Despesa', 'valor' => 1000],
['id' => 3, 'nome' => 'Compras', 'tipo' => 'Despesa', 'valor' => 500],
['id' => 4, 'nome' => 'Investimentos', 'tipo' => 'Receita', 'valor' => 1500]
];
$app->get('/lancamentos/{id}', function ($request, $response, $args) use (&$lancamentos) {
$lancamento = current(array_filter($lancamentos, fn ($p) => $p['id'] === (int) $args['id'])) ?: null;
if (!$lancamento) {
$response->getBody()->write(json_encode(['erro' => 'Lançamentos não encontrado']));
return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
}
$response->getBody()->write(json_encode($lancamento));
return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
});

$app->post('/lancamentos', function ($request, $response) use (&$lancamentos) {
$dados = $request->getParsedBody();
$novoLancamento = ['id' => count($lancamentos) + 1, 'nome' => $dados['nome'], 'tipo' => $dados['tipo'], 'valor' => $dados['valor']];
$lancamentos[] = $novoLancamento;
$response->getBody()->write(json_encode($novoLancamento));
return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});

$app->delete('/lancamentos/{id}', function ($request, $response, $args) use (&$lancamentos) {
$lancamentos = array_values(array_filter($lancamentos, fn ($p) => $p['id'] !== (int) $args['id']));
return $response->withStatus(204);
});






$app->run();