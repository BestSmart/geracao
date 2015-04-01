<?php

namespace Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping\OneToMany;

/**
 * @Entity @Table(name="carrousels")
 * */
class Carrousel extends DefaultModel {

	/** @Id @Column(type="integer") @GeneratedValue * */
	protected $id;

	/**
	 * @OneToMany(targetEntity="Slide", mappedBy="carrousel", cascade={"all"})
	 * 
	 * */
	protected $slides;

	public function __construct() {
		$this->slides = new ArrayCollection();
	}

	public function getId() {
		return $this->id;
	}

	public function getSlides() {
		return $this->slides;
	}

	public function addSlide($slide) {
		$this->slides[] = $slide;
	}

	public function setSlides($slides) {
		$this->slides = $slides;
		return $this;
	}

	public function expose() {
		$carrousel = parent::expose();
		$carrousel["slides"] = [];
		foreach ($this->slides as $slide) {
			$carrousel["slides"][] = $slide->expose();
		}
		return $carrousel;
	}

	public function fromArray($array) {
		unset($array['slides']);
		return parent::fromArray($array);
	}

}
