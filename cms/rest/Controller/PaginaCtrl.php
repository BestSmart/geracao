<?php

namespace Controller;

use DateTime;
use Exception;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;
use Model\Album;
use Model\Pagina;

class PaginaCtrl implements IController {

	private $paginaRepository;
	private $entityManager;
	private $printer;
	private $redirect_url;

	function __construct($printer, $entityManager) {
		$this->paginaRepository = $entityManager->getRepository("Model\Pagina");
		$this->entityManager = $entityManager;
		$this->printer = $printer;
	}

	function registrarPaginaFacebook($base_url, $id) {
		
	}

	function registerRoutes($base_url, $app) {
		$body = json_decode($app->request->getBody(), true);
		$app->get($base_url, function () {
			$paginas = $this->paginaRepository->findAll();
			$this->printer->printJsonResponse($paginas);
		});
		$app->get($base_url . 'facebookUrl/:id', function ($id) use($base_url) {
			$this->printer->printJsonResponse(['facebookUrl' => $this->registrarPaginaFacebook($base_url, $id)]);
		});
		$app->get($base_url . 'facebook/:id/', function ($id) use($pp) {
			FacebookSession::setDefaultApplication('659385950766405', '782cf3b317386136cd0130a475681c15');
			$helper = new FacebookRedirectLoginHelper($this->redirect_url);
			if (isset($_SESSION) && isset($_SESSION['fb_token'])) {
				// create new session from saved access_token
				$session = new FacebookSession($_SESSION['fb_token']);
				try {
					if (!$session->validate()) {
						$session = null;
					}
				} catch (Exception $e) {
					$session = null;
				}
			}

			if (!isset($session) || $session === null) {
				try {
					$session = $helper->getSessionFromRedirect();
				} catch (FacebookRequestException $ex) {
					print_r($ex);
				} catch (Exception $ex) {
					print_r($ex);
				}
			}
			// see if we have a session
			if (isset($session)) {

				// save the session
				$_SESSION['fb_token'] = $session->getToken();
				// create a session using saved token or the new one we generated at login
				$session = new FacebookSession($session->getToken());

				// graph api request for user data
				$request = new FacebookRequest($session, 'GET', '/me');
				$response = $request->execute();
				// get response
				$graphObject = $response->getGraphObject()->asArray();

				// print profile data
				echo '<pre>' . print_r($graphObject, 1) . '</pre>';

				// print logout url using session and redirect_uri (logout.php page should destroy the session)
				echo '<a href="' . $helper->getLogoutUrl($session, 'http://yourwebsite.com/app/logout.php') . '">Logout</a>';
			} else {
				// show login url
				echo '<a href="' . $this->redirect_url . '">Login</a>';
			}
		});
		$app->get($base_url . ':id', function ($id) {
			$this->printer->printJsonResponse($this->paginaRepository->find($id));
		});

		$app->get($base_url . ':id', function ($id) {
			$this->printer->printJsonResponse($this->paginaRepository->find($id));
		});
		$app->get($base_url . ':id/imagens', function ($id) {
			$produto = $this->paginaRepository->find($id);
			$album = $produto->getAlbum();
			$this->printer->printJsonResponse($this->imagemRepository->findByAlbum($album));
		});

		$app->post($base_url, function () use($body) {
			$pagina = (new Pagina())->fromArray($body);
			$dateTime = new DateTime("now");
			$pagina->setDataHora($dateTime);
			if ($pagina->getId() != NULL) {
				$tmp = $this->paginaRepository->find($pagina->getId())
						->setConteudo($pagina->getConteudo())
						->setPostagem($pagina->getPostagem())
						->setPublicar($pagina->getPublicar())
						->setOrdem($pagina->getOrdem())
						->setNomeMenu($pagina->getNomeMenu())
						->setTags($pagina->getTags())
						->setResumo($pagina->getResumo())
						->setTitulo($pagina->getTitulo());
				$pagina = $tmp;
			} else {
				$album = (new Album())
						->setNome('pagina-' . $pagina->getNomeMenu())
						->setPublicar(FALSE);
				$this->entityManager->persist($album);
				$pagina->setAlbum($album);
			}
			$this->entityManager->persist($pagina);
			$this->entityManager->flush();
			$this->printer->printJsonResponse($pagina);
		});
		$app->delete($base_url . ':id', function ($id) {
			$pagina = $this->paginaRepository->find($id);
			if ($pagina != NULL) {
				$this->entityManager->remove($pagina);
				$this->entityManager->flush();
			}
			$this->printer->printJsonResponse($pagina);
		});
	}

}
