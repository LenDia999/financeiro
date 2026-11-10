<?php

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Slim\App;
use Slim\Psr7\Factory\ServerRequestFactory;

class FinanceiroTest extends TestCase
{
    public function testStatus(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'GET', '/status');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(['status' => 'ok'], $this->decodificarResposta($response));
        $this->assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
    }

    public function testListaCategorias(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'GET', '/categorias');
        $categorias = $this->decodificarResposta($response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(5, $categorias);
        $this->assertSame('Salário', $categorias[0]['nome']);
    }

    public function testBuscaCategoriaPorId(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'GET', '/categorias/1');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            ['id' => 1, 'nome' => 'Salário', 'tipo' => 'Receita'],
            $this->decodificarResposta($response)
        );
    }

    public function testBuscaCategoriaInexistenteRetorna404(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'GET', '/categorias/999');

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(
            ['erro' => 'Categoria não encontrada'],
            $this->decodificarResposta($response)
        );
    }

    public function testCriaCategoria(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'POST', '/categorias', [
            'nome' => 'Transporte',
            'tipo' => 'Despesa',
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(
            ['id' => 6, 'nome' => 'Transporte', 'tipo' => 'Despesa'],
            $this->decodificarResposta($response)
        );
    }

    public function testAtualizaCategoria(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'PUT', '/categorias/2', [
            'nome' => 'Moradia',
            'tipo' => 'Despesa',
        ]);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame(
            ['id' => 2, 'nome' => 'Moradia', 'tipo' => 'Despesa'],
            $this->decodificarResposta($response)
        );
    }

    public function testAtualizaCategoriaInexistenteRetorna404(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'PUT', '/categorias/999', [
            'nome' => 'Moradia',
            'tipo' => 'Despesa',
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testExcluiCategoria(): void
    {
        $app = $this->criarApp();
        $response = $this->enviarRequisicao($app, 'DELETE', '/categorias/2');
        $consulta = $this->enviarRequisicao($app, 'GET', '/categorias/2');

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame(404, $consulta->getStatusCode());
    }

    public function testListaLancamentos(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'GET', '/lancamentos');
        $lancamentos = $this->decodificarResposta($response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertCount(8, $lancamentos);
        $this->assertSame('Novembro', $lancamentos[0]['nome']);
    }

    public function testBuscaLancamentoPorId(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'GET', '/lancamentos/1');

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Novembro', $this->decodificarResposta($response)['nome']);
    }

    public function testBuscaLancamentoInexistenteRetorna404(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'GET', '/lancamentos/999');

        $this->assertSame(404, $response->getStatusCode());
        $this->assertSame(
            ['erro' => 'Lançamento não encontrado'],
            $this->decodificarResposta($response)
        );
    }

    public function testCriaLancamento(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'POST', '/lancamentos', [
            'nome' => 'Conta de internet',
            'tipo' => 'Despesa',
            'valor' => 120,
            'id_categorias' => 2,
            'data' => '2023-12-08',
        ]);

        $this->assertSame(201, $response->getStatusCode());
        $this->assertSame(
            [
                'id' => 9,
                'nome' => 'Conta de internet',
                'tipo' => 'Despesa',
                'valor' => 120,
                'categorias' => 2,
                'data' => '2023-12-08',
            ],
            $this->decodificarResposta($response)
        );
    }

    public function testCriarLancamentoComCategoriaInexistenteRetorna400(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'POST', '/lancamentos', [
            'nome' => 'Despesa sem categoria',
            'tipo' => 'Despesa',
            'valor' => 50,
            'id_categorias' => 999,
            'data' => '2023-12-08',
        ]);

        $this->assertSame(400, $response->getStatusCode());
        $this->assertSame(
            ['erro' => 'Categoria não encontrada'],
            $this->decodificarResposta($response)
        );
    }

    public function testAtualizaLancamento(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'PUT', '/lancamentos/1', [
            'nome' => 'Salário atualizado',
            'tipo' => 'Receita',
            'valor' => 11000,
            'categorias' => 'Salário',
            'data' => '2023-11-30',
        ]);
        $lancamento = $this->decodificarResposta($response);

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Salário atualizado', $lancamento['nome']);
        $this->assertSame(11000, $lancamento['valor']);
    }

    public function testAtualizaLancamentoInexistenteRetorna404(): void
    {
        $response = $this->enviarRequisicao($this->criarApp(), 'PUT', '/lancamentos/999', [
            'nome' => 'Lançamento inexistente',
        ]);

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testExcluiLancamento(): void
    {
        $app = $this->criarApp();
        $response = $this->enviarRequisicao($app, 'DELETE', '/lancamentos/1');
        $consulta = $this->enviarRequisicao($app, 'GET', '/lancamentos/1');

        $this->assertSame(204, $response->getStatusCode());
        $this->assertSame(404, $consulta->getStatusCode());
    }

    private function criarApp(): App
    {
        require __DIR__ . '/../src/app.php';

        return $app;
    }

    private function enviarRequisicao(
        App $app,
        string $metodo,
        string $caminho,
        ?array $dados = null
    ): ResponseInterface {
        $request = (new ServerRequestFactory())->createServerRequest($metodo, $caminho);

        if ($dados !== null) {
            $request->getBody()->write(json_encode($dados, JSON_THROW_ON_ERROR));
            $request->getBody()->rewind();
            $request = $request->withHeader('Content-Type', 'application/json');
        }

        return $app->handle($request);
    }

    private function decodificarResposta(ResponseInterface $response): array
    {
        return json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }
}