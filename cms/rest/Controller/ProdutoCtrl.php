<?php

namespace Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Model\Album;
use Model\Detalhe;
use Model\Produto;

class ProdutoCtrl implements IController {

	private $entityManager;
	private $printer;
	private $produtoRepository;
	private $albumRepository;
	private $imagemRepository;
	private $detalheRepository;

	function __construct($printer, $entityManager) {
		$this->entityManager = $entityManager;
		$this->printer = $printer;
		$this->produtoRepository = $entityManager->getRepository('Model\Produto');
		$this->albumRepository = $entityManager->getRepository('Model\Album');
		$this->imagemRepository = $entityManager->getRepository('Model\Imagem');
		$this->detalheRepository = $entityManager->getRepository('Model\Detalhe');
	}

	function registerRoutes($base_url, $app) {
		$body = json_decode($app->request->getBody(), true);
		$app->get($base_url, function () {
			$this->printer->printJsonResponse($this->produtoRepository->findAll());
		});
		$app->get($base_url . ':id', function ($id) {
			$this->printer->printJsonResponse($this->produtoRepository->find($id));
		});
		$app->get($base_url . ':id/imagens', function ($id) {
			$produto = $this->produtoRepository->find($id);
			$album = $produto->getAlbum();
			$this->printer->printJsonResponse($this->imagemRepository->findByAlbum($album));
		});

		$app->post($base_url, function () use($body) {
			$produto = (new Produto())->fromArray($body);
			if ($produto->getId() <= 0) {
				$album = (new Album())
						->setNome('produto - ' . $produto->getNome())
						->setPublicar(FALSE);
				$this->entityManager->persist($album);
				$produto->setAlbum($album);
				$produto->setDetalhes(new ArrayCollection());
				foreach ($body['detalhes'] as $detalhe) {
					$produto->addDetalhe((new Detalhe())->fromArray($detalhe));
				};
			} else {
				$tmp = $this->produtoRepository->find($produto->getId());
				$tmp->setNome($produto->getNome())
						->setConteudo($produto->getConteudo())
						->setDestaque($produto->getDestaque())
						->setTags($produto->getTags())
						->setResumo($produto->getResumo());
				$detalhes = $tmp->getDetalhes();
				foreach ($detalhes as $detalhe) {
					$detalhes->removeElement($detalhe);
					$this->entityManager->remove($detalhe);
					$this->entityManager->flush();
				}

				$this->entityManager->flush();
				foreach ($body['detalhes'] as $detalhe) {
					$tmp->addDetalhe((new Detalhe())->fromArray($detalhe));
				};

				$produto = $tmp;
			}
			$this->entityManager->persist($produto);
			$this->entityManager->flush();
			$this->printer->printJsonResponse($produto);
		});
		$app->delete($base_url . ':id', function ($id) {
			$produto = $this->produtoRepository->find($id);
			$this->entityManager->getConnection()->beginTransaction(); // suspend auto-commit
			try {
				$detalhes = $produto->getDetalhes();
				foreach ($detalhes as $detalhe) {
					$detalhes->removeElement($detalhe);
					$this->entityManager->remove($detalhe);
					$this->entityManager->flush();
				}
				$this->entityManager->remove($produto);
				$this->entityManager->flush();
				$this->entityManager->getConnection()->commit();
			} catch (Exception $ex) {
				$this->entityManager->getConnection()->rollback();
				throw $ex;
			}
			$this->printer->printJsonResponse($produto);
		});
	}

}
