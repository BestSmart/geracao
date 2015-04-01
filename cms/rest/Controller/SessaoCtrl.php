<?php

namespace Controller;

use Model\Sessao;

class SessaoCtrl implements IController {

	private $entityManager;
	private $printer;
	private $sessaoRepository;

	function __construct($printer, $entityManager) {
		$this->entityManager = $entityManager;
		$this->printer = $printer;
		$this->sessaoRepository = $entityManager->getRepository("Model\Sessao");
	}

	function registerRoutes($base_url, $app) {
		$body = json_decode($app->request->getBody(), true);
		$app->get($base_url, function () {
			$this->printer->printJsonResponse($this->sessaoRepository->findAll());
		});
		$app->get($base_url . ':codigo', function ($codigo) {
			$this->printer->printJsonResponse($this->sessaoRepository->findByCodigo($codigo));
		});
		$app->post($base_url, function () use($body) {
			$sessao = (new Sessao())->fromArray($body);
			if ($sessao->getId() != NULL) {
				$tmp = $this->sessaoRepository->find($sessao->getId())
						->setCodigo($sessao->getCodigo())
						->setConteudo($sessao->getConteudo());
				$sessao = $tmp;
			}
			$this->entityManager->persist($sessao);
			$this->entityManager->flush();
			$this->printer->printJsonResponse($sessao);
		});
		$app->delete($base_url . ':id', function ($id) {
			$sessao = $this->sessaoRepository->find($id);
			if ($sessao != NULL) {
				$this->entityManager->remove($sessao);
				$this->entityManager->flush();
			}
			$this->printer->printJsonResponse($sessao);
		});
	}

}
