<?php

namespace Model;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * @Entity @Table(name="paginas")
 * */
class Pagina extends DefaultModel {

	/** @Id @Column(type="integer") @GeneratedValue * */
	protected $id;

	/** @Column(type="string") * */
	protected $titulo;

	/** @Column(type="string") * */
	protected $nomeMenu;

	/** @Column(type="text") * */
	protected $conteudo;

	/** @Column(type="text", nullable=true) * */
	protected $tags;

	/** @Column(type="text", nullable=true) * */
	protected $resumo;

	/** @Column(type="integer") * */
	protected $ordem;

	/** @Column(type="boolean") * */
	protected $publicar;

	/** @Column(type="boolean") * */
	protected $postagem;

	/** @Column(type="datetime") * */
	protected $dataHora;

	/**
	 * @OneToOne(targetEntity="Album")
	 * @JoinColumn(name="album_id", referencedColumnName="id")
	 * */
	protected $album;

	function __construct() {
		$this->sessoes = new ArrayCollection();
	}

	public function getId() {
		return $this->id;
	}

	public function getTitulo() {
		return $this->titulo;
	}

	public function getNomeMenu() {
		return $this->nomeMenu;
	}

	public function getConteudo() {
		return $this->conteudo;
	}

	public function setConteudo($conteudo) {
		$this->conteudo = $conteudo;
		return $this;
	}

	public function getOrdem() {
		return $this->ordem;
	}

	public function getPublicar() {
		return $this->publicar;
	}

	public function getPostagem() {
		return $this->postagem;
	}

	public function getDataHora() {
		return $this->dataHora;
	}

	public function setDataHora(DateTime $dataHora) {
		$this->dataHora = $dataHora;
		return $this;
	}

	public function setId($id) {
		$this->id = $id;
		return $this;
	}

	public function setTitulo($titulo) {
		$this->titulo = $titulo;
		return $this;
	}

	public function setNomeMenu($nomeMenu) {
		$this->nomeMenu = $nomeMenu;
		return $this;
	}

	public function setOrdem($ordem) {
		$this->ordem = $ordem;
		return $this;
	}

	public function setPublicar($publicar) {
		$this->publicar = filter_var($publicar, FILTER_VALIDATE_BOOLEAN);
		return $this;
	}

	public function setPostagem($postagem) {
		$this->postagem = filter_var($postagem, FILTER_VALIDATE_BOOLEAN);
		return $this;
	}

	public function getAlbum() {
		return $this->album;
	}

	public function setAlbum($album) {
		$this->album = $album;
		return $this;
	}

	public function getTags() {
		return $this->tags;
	}

	public function setTags($tags) {
		if (is_array($tags)) {
			$this->tags = implode(",", $tags);
		} else {
			$this->tags = $tags;
		}
		return $this;
	}
	
	public function getResumo() {
		return $this->resumo;
	}

	public function setResumo($resumo) {
		$this->resumo = $resumo;
		return $this;
	}



}
