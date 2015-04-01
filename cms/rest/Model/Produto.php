<?php

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\JoinTable;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @Entity @Table(name="produtos")
 * */
class Produto extends DefaultModel {

	/** @Id @Column(type="integer") @GeneratedValue * */
	protected $id;

	/** @Column(type="string") * */
	protected $nome;

	/**
	 * @OneToOne(targetEntity="Album")
	 * @JoinColumn(name="album_id", referencedColumnName="id")
	 * */
	protected $album;

	/** @Column(type="text") * */
	protected $conteudo;

	/** @Column(type="text", nullable=true) * */
	protected $tags;

	/** @Column(type="text") * */
	protected $resumo;

	/** @Column(type="boolean") * */
	protected $destaque;

	/**
	 * @ManyToMany(targetEntity="Detalhe", cascade={"all"})
	 * @JoinTable(name="produtos_detalhes",
	 *      joinColumns={@JoinColumn(name="produto_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@JoinColumn(name="detalhe_id", referencedColumnName="id")}
	 *      )
	 * */
	protected $detalhes;

	public function __construct() {
		$this->detalhes = new ArrayCollection();
	}

	public function getId() {
		return $this->id;
	}

	public function getNome() {
		return $this->nome;
	}

	public function getAlbum() {
		return $this->album;
	}

	public function getConteudo() {
		return $this->conteudo;
	}

	public function getDestaque() {
		return $this->destaque;
	}

	public function setNome($nome) {
		$this->nome = $nome;
		return $this;
	}

	public function setAlbum($album) {
		$this->album = $album;
		return $this;
	}

	public function setConteudo($conteudo) {
		$this->conteudo = $conteudo;
		return $this;
	}

	public function setDestaque($destaque) {
		$this->destaque = $destaque;
		return $this;
	}

	public function addDetalhe($detalhe) {
		$this->detalhes[] = $detalhe;
	}

	public function getDetalhes() {
		return $this->detalhes;
	}

	public function setDetalhes($detalhes) {
		$this->detalhes = $detalhes;
		return $this;
	}

	public function getTags() {
		return $this->tags;
	}

	public function getResumo() {
		return $this->resumo;
	}

	public function setTags($tags) {
		$this->tags = $tags;
		return $this;
	}

	public function setResumo($resumo) {
		$this->resumo = $resumo;
		return $this;
	}

	public function expose() {
		$produto = parent::expose();
		$produto["detalhes"] = [];
		foreach ($this->detalhes as $detalhe) {
			$produto["detalhes"][] = $detalhe->expose();
		}
		return $produto;
	}

	public function fromArray($array) {
		unset($array['detalhes']);
		return parent::fromArray($array);
	}

}
