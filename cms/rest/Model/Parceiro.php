<?php

use Model\DefaultModel;

namespace Model;

/**
 * @Entity @Table(name="parceiros")
 * */
class Parceiro extends DefaultModel {

    /** @Id @Column(type="integer") @GeneratedValue * */
    protected $id;

    /** @Column(type="string") * */
    protected $nome;

    /** @Column(type="string", nullable=true) * */
    protected $url;
	
    /** @Column(type="string", nullable=true) * */
    protected $slogan;
	
	/**
	 * @OneToOne(targetEntity="Album")
	 * @JoinColumn(name="album_id", referencedColumnName="id")
	 * */
	protected $album;

    public function getId() {
        return $this->id;
    }
	public function getNome() {
		return $this->nome;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getSlogan() {
		return $this->slogan;
	}

	public function getAlbum() {
		return $this->album;
	}

	public function setNome($nome) {
		$this->nome = $nome;
		return $this;
	}

	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	public function setSlogan($slogan) {
		$this->slogan = $slogan;
		return $this;
	}

	public function setAlbum($album) {
		$this->album = $album;
		return $this;
	}


}
