<?php

namespace Controller;

use Exception;
use Facebook\FacebookRedirectLoginHelper;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookSession;

class FacebookCtrl implements IController {

	private $paginaRepository;
	private $entityManager;
	private $printer;

	function __construct($printer, $entityManager) {
		$this->paginaRepository = $entityManager->getRepository("Model\Pagina");
		$this->entityManager = $entityManager;
		$this->printer = $printer;
	}

	function registerRoutes($base_url, $app) {
		$app->get($base_url . ':id', function ($id) {
			FacebookSession::setDefaultApplication('659385950766405', '782cf3b317386136cd0130a475681c15');
			$helper = new FacebookRedirectLoginHelper(full_path());
			try {
				$session = $helper->getSessionFromRedirect();
			} catch (FacebookRequestException $ex) {
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
			} catch (Exception $ex) {
				echo "Exception occured, code: " . $e->getCode();
				echo " with message: " . $e->getMessage();
			}
			if ($session) {
				try {
					$response = (new FacebookRequest(
							$session, 'POST', '/me/feed', [
						'link' => 'http://bestsmart.com.br/',
						'message' => 'Testanto integração do CMS com facebook'
							]
							))->execute()->getGraphObject();

					echo "Posted with id: " . $response->getProperty('id');
				} catch (FacebookRequestException $e) {

					echo "Exception occured, code: " . $e->getCode();
					echo " with message: " . $e->getMessage();
				}
			} else {
				header('Location:' . $helper->getLoginUrl());
			}
		});
	}

}
