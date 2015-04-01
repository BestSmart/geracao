<?php

namespace Controller;

use Doctrine\ORM\EntityManager;
use Model\Album;

class AlbumCtrl implements IController {

    private $entityManager;
    private $albumRepository;
    private $printer;

    function __construct(Printer $printer, EntityManager $entityManager) {
        $this->printer = $printer;
        $this->entityManager = $entityManager;
        $this->albumRepository = $entityManager->getRepository("Model\Album");
    }

    function albumExists($album) {
        return ($this->albumRepository->findByNome($album->getNome()) != NULL);
    }

    function registerRoutes($base_url, $app) {
        $body = json_decode($app->request->getBody(), true);
        $app->get($base_url, function () {
            $this->printer->printJsonResponse($this->albumRepository->findAll());
        });
        $app->get($base_url . ':id', function ($id) {
            $this->printer->printJsonResponse($this->albumRepository->find($id));
        });

        $app->post($base_url, function ()use($body) {

            $album = (new Album())->fromArray($body);
            if (!isset($body['id']) && $this->albumExists($album)) {
                $album = ["msg" => "Album com o mesmo nome encontrado"];
            } else {
				$tmp = $this->albumRepository->find($body['id']);
                if ($tmp != NULL) {
                    $tmp->setNome($body['nome']);
                    $tmp->setDescricao($body['descricao']);
                    $tmp->setPublicar(filter_var($body['publicar'], FILTER_VALIDATE_BOOLEAN)); // true;
					$album = $tmp;
                }
                $this->entityManager->persist($album);
                $this->entityManager->flush();
            }
            $this->printer->printJsonResponse($album);
        });
        $app->delete($base_url . ':id', function ($id) {
            $album = $this->albumRepository->find($id);
            if ($album != NULL) {
                $this->entityManager->remove($album);
                $this->entityManager->flush();
            }
            $this->printer->printJsonResponse($album);
        });
    }

}
