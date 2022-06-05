<?php

namespace Alura\Cursos\Controller;

use Alura\Cursos\Entity\Curso;
use Alura\Cursos\Infra\EntityManagerCreator;

class Persistencia implements InterfaceControladorRequisicao
{
    private $entityManager;

    public function __construct()
    {
        $this->entityManager = (new EntityManagerCreator())->getEntityManager();
    }

    public function processaRequisicao(): void
    {
        $descricao = filter_input(
            INPUT_POST,
            'descricao'
        );

        $descricao = $this->filter_string_polyfill($descricao);

        $curso = new Curso();
        $curso->setDescricao($descricao);
        $this->entityManager->persist($curso);
        $this->entityManager->flush();

        header('Location: /listar-cursos', true, 302);
    }

    function filter_string_polyfill(string $string): string
    {
        $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
        return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
    }
}
