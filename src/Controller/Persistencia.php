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

        $id = filter_input(
            INPUT_GET,
            'id',
            FILTER_VALIDATE_INT
        );

        if (!is_null($id)) {
            $curso->setId($id);
            $this->entityManager->merge($curso);
            $_SESSION['mensagem'] = "Curso atualizado com sucesso";
        } else {
            $this->entityManager->persist($curso);
            $_SESSION['mensagem'] = "Curso inserido com sucesso";
        }
        $_SESSION['tipo_mensagem'] = 'success';

        $this->entityManager->flush();

        header('Location: /listar-cursos', true, 302);
    }

    public function filter_string_polyfill(string $string): string
    {
        $str = preg_replace('/\x00|<[^>]*>?/', '', $string);
        return str_replace(["'", '"'], ['&#39;', '&#34;'], $str);
    }
}
