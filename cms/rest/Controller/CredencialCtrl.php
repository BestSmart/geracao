<?php

namespace Controller;

use Controller\IController;
use Controller\Printer;
use Doctrine\ORM\EntityManager;
use Model\Credencial;

class CredencialCtrl implements IController {

    private $repository;
    private $printer;
    private $entityManager;

    const HASH_ALG = 'sha512';

    function __construct(Printer $printer, EntityManager $entityManager) {
        $this->entityManager = $entityManager;
        $this->repository = $entityManager->getRepository("Model\Credencial");
        $this->printer = $printer;
    }

    private function saveNewCredencial(Credencial $credencial) {
        $random_salt = hash(self::HASH_ALG, uniqid(mt_rand(1, mt_getrandmax()), true));
        $credencial->setSalt($random_salt)
                ->setSenha(hash(self::HASH_ALG, $credencial->getSenha() . $random_salt));
        $this->entityManager->persist($credencial);
        $this->entityManager->flush();
        $senha = substr(str_shuffle(md5(time())),0,8);
        $this->enviarNovaSenha($credencial, $senha);
        return $credencial;
    }
    
    private function enviarNovaSenha($credencial, $senha){
        $to      =  $credencial->getEmail();
        $subject = 'Nova Senha';
        
        $message = 'Foi gerada uma nova senha para o usuário: '.$credencial->getLogin().' a senha é: '.$senha;
        $headers = 'From: contato@bestsmart.com.br' . "\r\n" .
            'Reply-To: contato@bestsmart.com.br' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();

        mail($to, $subject, $message, $headers);
   
        $random_salt = hash(self::HASH_ALG, uniqid(mt_rand(1, mt_getrandmax()), true));
        var_dump($senha);
        $credencial->setSalt($random_salt)
                    ->setSenha(hash(self::HASH_ALG, $senha . $random_salt));
        $this->entityManager->persist($credencial);
        $this->entityManager->flush();
        
    }

    private function updateCredencial($credencial, $body) {
        $tmp = (new Credencial())->fromArray($body);
        $senhaAtual = "";
        if (isset($body ['senhaAtual'])) {
            $senhaAtual = hash('sha512', $body ['senhaAtual'] . $credencial->getSalt());

            if ($credencial->getSenha() != null && $credencial->getSenha() == $senhaAtual) {
				$senha =  $tmp->getSenha();
                $random_salt = hash(self::HASH_ALG, uniqid(mt_rand(1, mt_getrandmax()), true));
                $credencial->setSalt($random_salt)
                        ->setSenha(hash(self::HASH_ALG, $senha. $random_salt));
				$this->enviarNovaSenha($credencial, $senha);
            }
        }
        $this->entityManager->persist($credencial);
        $this->entityManager->flush();
        return $credencial;
    }

    function registerRoutes($base_url, $app) {
        $body = json_decode($app->request->getBody(), true);

        $app->get($base_url, function () {
            $this->printer->printJsonResponse($this->repository->find($_SESSION ['user_id']));
        });
        $app->get($base_url.'all/', function () {
            $this->printer->printJsonResponse($this->repository->findAll());
        });

        $app->post($base_url.'novaSenha', function () use($body) {
            $id = "";
            if (isset($body['id'])) {
                $id = $body['id'];
            }
            $credencial = $this->repository->find($id);
            if ($credencial != NULL) {
				$senha = substr(str_shuffle(md5(time())),0,8);
                $this->enviarNovaSenha($credencial, $senha);
            }
            
            $this->printer->printJsonResponse($credencial);
        });
        $app->post($base_url.'alterarSenha', function () use($body) {
            $id = "";
            if (isset($_SESSION ['user_id'])) {
                $id = $_SESSION ['user_id'];
            }
            $credencial = $this->repository->find($id);
            if ($credencial != NULL) {
                 $this->updateCredencial($credencial, $body);
            }
            
            $this->printer->printJsonResponse($credencial);
        });
        $app->post($base_url, function () use($body) {
            $response = "";
            $id = "";
            if (isset($body['id'])) {
                $id = $body['id'];
            }
            $credencial = $this->repository->find($id);
            if ($credencial == NULL) {
                if (isset($body['email'])) {
                    $credencial = $this->repository->findOneByEmail($body['email']);
                }
                if (isset($body['login'])) {
                    $credencial = $this->repository->findOneByLogin($body['login']);
                }
                if ($credencial == NULL) {
                    $credencial = (new Credencial())->fromArray($body);
                    $response = $this->saveNewCredencial($credencial);
                } else {
                    $response = ["msg" => "Usuário já existe"];
                }
            } else {
                $response = $this->updateCredencial($credencial, $body);
            }
            $this->printer->printJsonResponse($response);
        });
        $app->delete($base_url . ':id', function ($id) {
            $credencial = $this->repository->find($id);
            $response = $credencial;
            if ($credencial == NULL) {
                $response = ["msg" => "Credencial não encontrada!"];
            } else {
                $this->entityManager->remove($credencial);
                $this->entityManager->flush();
            }
            $this->printer->printJsonResponse($response);
        });
    }

}
