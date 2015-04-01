<?php

use Model\DefaultModel;

namespace Model;

/**
 * @Entity @Table(name="sessoes")
 * */
class Sessao extends DefaultModel {

    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;

    /** @Column(type="string") * */
    protected $codigo;

    /** @Column(type="text") * */
    protected $conteudo;

    public function getId() {
        return $this->id;
    }

    public function getCodigo() {
        return $this->codigo;
    }

    public function getConteudo() {
        return $this->conteudo;
    }

    public function setId($id) {
        $this->id = $id;
        return $this;
    }

    public function setCodigo($codigo) {
        $this->codigo = $codigo;
        return $this;
    }

    public function setConteudo($conteudo) {
        $this->conteudo = $conteudo;
        return $this;
    }

}
