<?php

namespace Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Model\Album;
use Model\Parceiro;

class ParceiroCtrl implements IController {

	private $entityManager;
	private $printer;
	private $parceiroRepository;
	private $albumRepository;
	private $imagemRepository;
	private $detalheRepository;

	function __construct($printer, $entityManager) {
		$this->entityManager = $entityManager;
		$this->printer = $printer;
		$this->parceiroRepository = $entityManager->getRepository('Model\Parceiro');
		$this->albumRepository = $entityManager->getRepository('Model\Album');
		$this->imagemRepository = $entityManager->getRepository('Model\Imagem');
		$this->detalheRepository = $entityManager->getRepository('Model\Detalhe');
	}

	function registerRoutes($base_url, $app) {
		$body = json_decode($app->request->getBody(), true);
		$app->get($base_url, function () {
			$this->printer->printJsonResponse($this->parceiroRepository->findAll());
		});
		$app->get($base_url . ':id', function ($id) {
			$this->printer->printJsonResponse($this->parceiroRepository->find($id));
		});
		$app->get($base_url . ':id/imagens', function ($id) {
			$parceiro = $this->parceiroRepository->find($id);
			$album = $parceiro->getAlbum();
			$this->printer->printJsonResponse($this->imagemRepository->findByAlbum($album));
		});

		$app->post($base_url, function () use($body) {
			$parceiro = (new Parceiro())->fromArray($body);
			if ($parceiro->getId() <= 0) {
				$album = (new Album())
						->setNome('parceiro - ' . $parceiro->getNome())
						->setPublicar(FALSE);
				$this->entityManager->persist($album);
				$parceiro->setAlbum($album);
			} else {
				$tmp = $this->parceiroRepository->find($parceiro->getId());
				$tmp->setNome($parceiro->getNome())
						->setUrl($parceiro->getUrl())
						->setSlogan($parceiro->getSlogan());
				$this->entityManager->flush();
				$parceiro = $tmp;
			}
			$this->entityManager->persist($parceiro);
			$this->entityManager->flush();
			$this->printer->printJsonResponse($parceiro);
		});
		$app->delete($base_url . ':id', function ($id) {
			$parceiro = $this->parceiroRepository->find($id);
			$this->entityManager->getConnection()->beginTransaction();
			try {
				$this->entityManager->remove($parceiro);
				$this->entityManager->flush();
				$this->entityManager->getConnection()->commit();
			} catch (Exception $ex) {
				$this->entityManager->getConnection()->rollback();
				throw $ex;
			}
			$this->printer->printJsonResponse($parceiro);
		});
	}

}
