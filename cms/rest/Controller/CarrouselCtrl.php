<?php

namespace Controller;

use Doctrine\Common\Util\Debug;
use Model\Carrousel;
use Model\Slide;

class CarrouselCtrl implements IController {

	private $entityManager;
	private $printer;
	private $carrouselRepository;
	private $slideRepository;

	function __construct($printer, $entityManager) {
		$this->entityManager = $entityManager;
		$this->printer = $printer;
		$this->carrouselRepository = $entityManager->getRepository('Model\Carrousel');
		$this->slideRepository = $entityManager->getRepository('Model\Slide');

		if ($this->carrouselRepository->find(1) == NULL) {
			$carrousel = (new Carrousel());
			$this->criarCarrousel($carrousel);
		}
	}

	function criarCarrousel($carrousel) {
		$this->entityManager->persist($carrousel);
		$this->entityManager->flush();
	}

	function registerRoutes($base_url, $app) {
		$body = json_decode($app->request->getBody(), true);
		$app->get($base_url, function () {
			$carrousel = $this->carrouselRepository->find(1);
			$this->printer->printJsonResponse($carrousel);
		});

		$app->post($base_url, function () use($body) {
			$tmp = $this->carrouselRepository->find(1);
			$slides = $tmp->getSlides();
			foreach ($slides as $slide) {
				$slides->removeElement($slide);
				$this->entityManager->remove($slide);
				$this->entityManager->flush();
			}
			$this->entityManager->persist($tmp);
			$this->entityManager->flush();
			foreach ($body['slides'] as $slide) {
				$slide = (new Slide())->fromArray($slide)->setCarrousel($tmp);
				$this->entityManager->persist($slide);
				$this->entityManager->flush();
			}
			Debug::dump($body['slides']);
			$this->printer->printJsonResponse($tmp);
		});
	}

}
