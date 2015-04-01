<?php

namespace Controller;

use Doctrine\ORM\EntityManager;
use Model\Imagem;
use Resizer;

class ImagemCtrl implements IController {

	private $entityManager;
	private $printer;
	private $imagemRepository;
	private $albumRepository;

	const IMG_FOLDER = "../../img/uploaded/";
	const IMG_FOLDER_THUMBNAIL = "../../img/uploaded/thumbnail/";

	function __construct(Printer $printer, EntityManager $entityManager) {
		$this->printer = $printer;
		$this->entityManager = $entityManager;
		$this->imagemRepository = $entityManager->getRepository("Model\Imagem");
		$this->albumRepository = $entityManager->getRepository("Model\Album");
	}

	function removeImage($image) {
		$thumbnailDestination = realpath($image->getThumbnail());
		$destination = realpath($image->getUrl());
		unlink($thumbnailDestination);
		unlink($destination);
	}

	function saveImage($tempName, $ext, $albumId) {
		$name = sha1(uniqid() . sha1_file($tempName)) . '.png';

		// *** 1) Initialize / load image
		$resizeObj = new Resizer($tempName, $ext);
// *** 2) Resize image (options: exact, portrait, landscape, auto, crop)
		if ($resizeObj->largeThan(1600, 1200)) {
			$resizeObj->resizeImage(1600, 1200, 'auto');
		} else {
			$resizeObj->keepSize();
		}
// *** 3) Save image

		if (!file_exists('./' . self::IMG_FOLDER . $albumId . "/")) {
			mkdir('./' . self::IMG_FOLDER . $albumId . "/", 0777, true);
		}
		if (!file_exists('./' . self::IMG_FOLDER_THUMBNAIL . $albumId . "/")) {
			mkdir('./' . self::IMG_FOLDER_THUMBNAIL . $albumId . "/", 0777, true);
		}
		$thumbnailDestination = realpath(self::IMG_FOLDER_THUMBNAIL) . "/" . $albumId . "/" . $name;
		$destination = realpath(self::IMG_FOLDER) . "/" . $albumId . "/" . $name;
		$resizeObj->saveImage($destination, 90);

		$resizeThumbnail = new Resizer($tempName, $ext);
		$resizeThumbnail->resizeImage(215, 160, 'crop');
		$resizeThumbnail->saveImage($thumbnailDestination, 90);

		$imagem = new Imagem();
		$album = $this->albumRepository->find($albumId);
		if ($album == NULL) {
			$album = $this->albumRepository->find(1);
		}
		$imagem->setAlbum($album)
				->setUrl(self::IMG_FOLDER . $albumId . "/" . $name)
				->setThumbnail(self::IMG_FOLDER_THUMBNAIL . $albumId . "/" . $name)
				->setAbsolutePath($destination)
				->setCapa(false);

		$this->entityManager->persist($imagem);
		$this->entityManager->flush();
		$this->printer->printJsonResponse($imagem);
	}

	private function uploadImage($error, $name, $tempName, $albumId) {
		$ext = explode('.', $name);
		$ext = $ext [sizeof($ext) - 1];

		if ($error !== UPLOAD_ERR_OK) {
			return "Falha ao fazer upload do arquivo: " . $error;
		}
		$info = getimagesize($tempName);
		if ($info === FALSE) {
			return "arquivo não é uma imagem válida";
		}

		if (($info[2] !== IMAGETYPE_GIF) && ($info[2] !== IMAGETYPE_JPEG) && ($info[2] !== IMAGETYPE_PNG)) {
			return"imagem deve ser gif/jpg/jpeg/png";
		}
		$this->saveImage($tempName, $ext, $albumId);
		return "Sucesso!";
	}

	function registerRoutes($base_url, $app) {
		$body = json_decode($app->request->getBody(), true);
		$app->post($base_url . 'capa/:id', function ($id) {
			$imagem = $this->imagemRepository->find($id);
			$qb = $this->entityManager->createQueryBuilder();
			$qb
					->update('Model\Imagem', 'i')
					->set('i.capa', $qb->expr()->literal(FALSE))
					->where('i.album = ?1');
			$qb->setParameters(['1' => $imagem->getAlbum()]);
			$qb->getQuery()->getResult();

			$imagem->setCapa(true);
			$this->entityManager->persist($imagem);
			$this->entityManager->flush();
		});
		$app->get($base_url . ':album', function ($albumId) {
			$qb = $this->entityManager->createQueryBuilder();
			$imagens = $qb->select('e')
					->from('Model\Imagem', 'e')
					->where('e.album = ?1')
					->orderby('e.capa', 'DESC')
					->setParameter(1, $albumId)
					->getQuery()
					->getResult();
			$this->printer->printJsonResponse($imagens);
		});
		$app->post($base_url . ':album', function ($albumId) use($body, $app) {
			$errors = [];
			if (is_array($_FILES['files']['tmp_name'])) {
				foreach ($_FILES['files']['tmp_name'] as $key => $tempName) {
					$name = $_FILES['files']['name'][$key];
					$tempName = $_FILES['files']['tmp_name'][$key];
					$error = $_FILES['files']['error'][$key];
					$errors[$name] = $this->uploadImage($error, $name, $tempName, $albumId);
				}
			} else {
				$name = $_FILES['file']['name'];
				$tempName = $_FILES['file']['tmp_name'];
				$error = $_FILES['file']['error'];
				$errors[$name] = $this->uploadImage($error, $name, $tempName, $albumId);
			}
		});
		$app->delete($base_url . ':id', function ($id) {
			$imagem = $this->imagemRepository->find($id);
			if ($imagem != NULL) {
				$this->entityManager->remove($imagem);
				$this->entityManager->flush();
				$this->removeImage($imagem);
			}
			$this->printer->printJsonResponse($imagem);
		});
	}

}

