<?php

use Model\DefaultModel;

namespace Model;

/**
 * @Entity @Table(name="detalhes")
 * */
class Detalhe extends DefaultModel {

	/** @Id @Column(type="integer") @GeneratedValue * */
	protected $id;

	/** @Column(type="string") * */
	protected $label;

	/** @Column(type="text") * */
	protected $conteudo;


	public function getId() {
		return $this->id;
	}

	public function getLabel() {
		return $this->label;
	}

	public function getConteudo() {
		return $this->conteudo;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setLabel($label) {
		$this->label = $label;
		return $this;
	}

	public function setConteudo($conteudo) {
		$this->conteudo = $conteudo;
		return $this;
	}

}
