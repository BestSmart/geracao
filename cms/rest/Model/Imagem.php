<?php

namespace Model;

use Doctrine\ORM\Mapping\ManyToOne;

/**
 * @Entity @Table(name="imagens")
 * */
class Imagem extends DefaultModel {

	/** @Id @Column(type="integer") @GeneratedValue * */
	protected $id;

	/** @Column(type="string") * */
	protected $url;

	/** @Column(type="string") * */
	protected $thumbnail;

	/** @Column(type="boolean") * */
	protected $capa;

	/**
	 * @ManyToOne(targetEntity="Album", inversedBy="imagens")
	 * @JoinColumn(name="album_id", referencedColumnName="id", nullable=true)
	 * */
	protected $album;

	/** @Column(type="string") * */
	protected $absolutePath;

	public function getId() {
		return $this->id;
	}

	public function getUrl() {
		return $this->url;
	}

	public function getCapa() {
		return $this->capa;
	}

	public function getAlbum() {
		return $this->album;
	}

	public function getAbsolutePath() {
		return $this->absolutePath;
	}

	public function setUrl($url) {
		$this->url = $url;
		return $this;
	}

	public function setCapa($capa) {
		$this->capa = $capa;
		return $this;
	}

	public function setAlbum($album) {
		$this->album = $album;
		return $this;
	}

	public function setAbsolutePath($absolutePath) {
		$this->absolutePath = $absolutePath;
		return $this;
	}

	function getThumbnail() {
		return $this->thumbnail;
	}

	function setThumbnail($thumbnail) {
		$this->thumbnail = $thumbnail;
		return $this;
	}

	public function expose() {
		$array = parent::expose();
		unset($array['absolutePath']);
		return $array;
	}

}
