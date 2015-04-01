<?php

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity @Table(name="albuns")
 * */
class Album extends DefaultModel {

	/** @Id @Column(type="integer") @GeneratedValue * */
	protected $id;

	/** @Column(type="string") * */
	protected $nome;

	/** @Column(type="string", nullable=true) * */
	protected $descricao;

	/** @Column(type="boolean") * */
	protected $publicar;

	/**
	 * @OneToMany(targetEntity="Imagem", mappedBy="album")
	 * 
	 * */
	protected $imagens;

	public function __construct() {
		$this->imagens = new ArrayCollection();
	}

	public function getId() {
		return $this->id;
	}

	public function getNome() {
		return $this->nome;
	}

	public function getPublicar() {
		return $this->publicar;
	}

	public function getImagens() {
		return $this->imagens;
	}

	public function setNome($nome) {
		$this->nome = $nome;
		return $this;
	}

	public function setPublicar($publicar) {
		$this->publicar = $publicar;
		return $this;
	}

	public function setImagens($imagens) {
		$this->imagens = $imagens;
		return $this;
	}

	public function addImagem(Imagem $imagem) {
		$this->imagens[] = $imagem;
	}

	public function getDescricao() {
		return $this->descricao;
	}

	public function setDescricao($descricao) {
		$this->descricao = $descricao;
		return $this;
	}

	public function getCapa() {
		return $this->imagens->filter(function($imagem) {
					return $imagem->getCapa();
				})->first();
	}

}
