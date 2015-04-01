<?php

namespace Controller;

use Controller\Printer;
use DateTime;
use Doctrine\ORM\EntityManager;
use Model\TentativasLogin;

class LoginCtrl implements IController {

    private $printer;
    private $credencialRepository;
    private $tentativasLoginRepository;
    private $entityManager;

    public function __construct(Printer $printer, EntityManager $entityManager) {
        $this->printer = $printer;
        $this->entityManager = $entityManager;
        $this->credencialRepository = $entityManager->getRepository('Model\Credencial');
        $this->tentativasLoginRepository = $entityManager->getRepository('Model\TentativasLogin');
    }

    public function registerRoutes($base_url, $app) {
        $body = json_decode($app->request->getBody(), true);
        $entrar = function () use ($body, $app){
            $response = ['message' => $this->login($body['username'], $body['password'])];
            if($response['message'] != 'sucesso'){
                $app->halt(500, json_encode($response));
            }else{
                $this->printer->printJsonResponse($response);
            }
        };
        $isLogado = function() {
            $response = ['logged' => $this->loginCheck()];
            $this->printer->printJsonResponse($response);
        };

        $app->get($base_url, $isLogado);
        $app->post($base_url, $entrar);
        return $this;
    }

    public function login($login, $senha) {
        $credencial = $this->credencialRepository->findOneByLogin($login);
        if (!is_array($credencial) && $credencial != NULL) {
            $senha = hash('sha512', $senha . $credencial->getSalt());
            if ($credencial->getSenha() === $senha) {
                // Password is correct!
                // Get the user-agent string of the user.
                $user_browser = $_SERVER ['HTTP_USER_AGENT'];
                // XSS protection as we might print this value
                $user_id = preg_replace("/[^0-9]+/", "", $credencial->getId());
                $_SESSION ['user_id'] = $user_id;
                // XSS protection as we might print this value
                $username = $credencial->getEmail();
                $_SESSION ['username'] = $username;
                $_SESSION ['login_string'] = hash('sha512', $credencial->getSenha() . $user_browser . $credencial->getSalt());
                return 'sucesso';
            } else {
                $this->entityManager->persist((new TentativasLogin())
                                ->setCredencial($credencial)
                                ->setTime(new DateTime("now")));
                $this->entityManager->flush();
            }
        }
        return 'Nome de usuário e/ou senha incorretos';
    }

    public function loginCheck() {
        if (isset($_SESSION ['user_id'], $_SESSION ['username'], $_SESSION ['login_string'])) {
            $user_id = $_SESSION ['user_id'];
            $login_string = $_SESSION ['login_string'];

            $user_browser = $_SERVER ['HTTP_USER_AGENT'];

            $credencial = $this->credencialRepository->find($user_id);

            if (isset($credencial)) {
                $login_check = hash('sha512', $credencial->getSenha() . $user_browser . $credencial->getSalt());
                return $login_check == $login_string;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
