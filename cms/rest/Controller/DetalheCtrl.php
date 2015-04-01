<?php

namespace Controller;

use Model\Detalhe;

class DetalheCtrl implements IController {

	private $entityManager;
	private $printer;
	private $detalheRepository;

	function __construct($printer, $entityManager) {
		$this->entityManager = $entityManager;
		$this->printer = $printer;
		$this->detalheRepository = $entityManager->getRepository("Model\Detalhe");
	}

	function registerRoutes($base_url, $app) {
		$body = json_decode($app->request->getBody(), true);
		$app->get($base_url, function () {
			$this->printer->printJsonResponse($this->detalheRepository->findAll());
		});
		$app->get($base_url . ':label', function ($label) {
			$this->printer->printJsonResponse($this->detalheRepository->findByLabel($label));
		});
		$app->post($base_url, function () use($body) {
			$detalhe = (new Sessao())->fromArray($body);
			if ($detalhe->getId() != NULL) {
				$tmp = $this->detalheRepository->find($detalhe->getId())
						->setLabel($detalhe->getLabel())
						->setConteudo($detalhe->getConteudo());
				$detalhe = $tmp;
			}
			$this->entityManager->persist($detalhe);
			$this->entityManager->flush();
			$this->printer->printJsonResponse($detalhe);
		});
		$app->delete($base_url . ':id', function ($id) {
			$detalhe = $this->detalheRepository->find($id);
			if ($detalhe != NULL) {
				$this->entityManager->remove($detalhe);
				$this->entityManager->flush();
			}
			$this->printer->printJsonResponse($detalhe);
		});
	}

}
