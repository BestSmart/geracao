<?php

namespace Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Exception;
use Model\Album;
use Model\Detalhe;
use Model\Empresa;

class EmpresaCtrl implements IController {

	private $entityManager;
	private $printer;
	private $empresaRepository;
	private $albumRepository;
	private $imagemRepository;
	private $detalheRepository;

	function __construct($printer, $entityManager) {
		$this->entityManager = $entityManager;
		$this->printer = $printer;
		$this->empresaRepository = $entityManager->getRepository('Model\Empresa');
		$this->albumRepository = $entityManager->getRepository('Model\Album');
		$this->imagemRepository = $entityManager->getRepository('Model\Imagem');
		$this->detalheRepository = $entityManager->getRepository('Model\Detalhe');

		if ($this->empresaRepository->find(1) == NULL) {
			$empresa = (new Empresa())
					->setDominio("seudominio.com.br")
					->setEmailContato("contato@seudominio.com.br")
					->setEndereco("Aqui vem seu endereço")
					->setHistoria("Escreva sua história da forma que você gostaria que os seus usuários vejam!")
					->setMissao("Escreva a missão a empresa")
					->setVisao("Escreva a visão da empresa")
					->setValores("Escreva os valores separados por vírgula")
					->setNome("Escreva o nome da sua Empresa")
					->setResumo("Faça um resumo da sua Empresa da forma que você gostaria que seus usuários vejam")
					->setTelefone("Telefone da Empresa");
			$this->criarEmpresa($empresa);
		}
	}

	function criarEmpresa($empresa) {
		$album = (new Album())
				->setNome('empresa - ' . $empresa->getNome())
				->setPublicar(FALSE);
		$this->entityManager->persist($album);
		$empresa->setAlbum($album);
		$empresa->setDetalhes(new ArrayCollection());
		$this->entityManager->persist($empresa);
		$this->entityManager->flush();
	}

	function registerRoutes($base_url, $app) {
		$body = json_decode($app->request->getBody(), true);
		$app->get($base_url, function () {
			$this->printer->printJsonResponse($this->empresaRepository->find(1));
		});

		$app->post($base_url, function () use($body) {
			$empresa = (new Empresa())->fromArray($body);
			$tmp = $this->empresaRepository->find(1);
			$tmp->setNome($empresa->getNome())
					->setHistoria($empresa->getHistoria())
					->setResumo($empresa->getResumo())
					->setSlogan($empresa->getSlogan())
					->setMissao($empresa->getMissao())
					->setVisao($empresa->getVisao())
					->setValores($empresa->getValores())
					->setIdGoogleAnalytics($empresa->getIdGoogleAnalytics())
					->setDominio($empresa->getDominio())
					->setTelefone($empresa->getTelefone())
					->setEndereco($empresa->getEndereco())
					->setEmailContato($empresa->getEmailContato());
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
			$this->entityManager->persist($tmp);
			$this->entityManager->flush();
			$this->printer->printJsonResponse($tmp);
		});
	}

}
