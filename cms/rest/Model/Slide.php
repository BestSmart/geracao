<?php

use Model\DefaultModel;

namespace Model;

require_once 'DefaultModel.php';

/**
 * @Entity @Table(name="slides")
 * */
class Slide extends DefaultModel {

	/** @Id @Column(type="integer") @GeneratedValue * */
	protected $id;

	/** @Column(type="string") * */
	protected $linkImagem;

	/** @Column(type="text") * */
	protected $texto;

	/** @Column(type="integer", nullable=true) * */
	protected $ordem;

	/** @Column(type="string", nullable=true) * */
	protected $titulo;

	/** @Column(type="string", nullable=true) * */
	protected $redirect;

	/**
	 * @ManyToOne(targetEntity="Carrousel", inversedBy="slides")
	 * @JoinColumn(name="correousel_id", referencedColumnName="id", nullable=false)
	 * */
	protected $carrousel;

	public function getId() {
		return $this->id;
	}

	public function getLinkImagem() {
		return $this->linkImagem;
	}

	public function getTexto() {
		return $this->texto;
	}

	public function getTitulo() {
		return $this->titulo;
	}

	public function getCarrousel() {
		return $this->carrousel;
	}

	public function setLinkImagem($linkImagem) {
		$this->linkImagem = $linkImagem;
		return $this;
	}

	public function setTexto($texto) {
		$this->texto = $texto;
		return $this;
	}

	public function setTitulo($titulo) {
		$this->titulo = $titulo;
		return $this;
	}

	public function setCarrousel($carrousel) {
		$this->carrousel = $carrousel;
		return $this;
	}

	public function getOrdem() {
		return $this->ordem;
	}

	public function setOrdem($ordem) {
		$this->ordem = $ordem;
		return $this;
	}

	public function getRedirect() {
		return $this->redirect;
	}

	public function setRedirect($redirect) {
		$this->redirect = $redirect;
		return $this;
	}
	
	public function expose() {
		$carousel = $this->carrousel;
		$this->carrousel = null;
		$slide =  parent::expose();
		$this->carrousel = $carousel;
		return $slide;
	}

}
