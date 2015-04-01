<?php

require_once 'bootstrap.php';
require_once 'Resizer.php';
require_once 'Model/DefaultModel.php';
require_once 'Model/Credencial.php';
require_once 'Model/TentativasLogin.php';
require_once 'Model/Imagem.php';
require_once 'Model/Album.php';
require_once 'Model/Pagina.php';
require_once 'Model/Produto.php';
require_once 'Model/Sessao.php';
require_once 'Model/Detalhe.php';
require_once 'Model/Empresa.php';
require_once 'Model/Carrousel.php';
require_once 'Model/Parceiro.php';
require_once 'Model/Slide.php';


require_once 'Controller/IController.php';
require_once 'Controller/Printer.php';
require_once 'Controller/LoginCtrl.php';
require_once 'Controller/CredencialCtrl.php';
require_once 'Controller/ImagemCtrl.php';
require_once 'Controller/AlbumCtrl.php';
require_once 'Controller/PaginaCtrl.php';
require_once 'Controller/ProdutoCtrl.php';
require_once 'Controller/SessaoCtrl.php';
require_once 'Controller/DetalheCtrl.php';
require_once 'Controller/FacebookCtrl.php';
require_once 'Controller/EmpresaCtrl.php';
require_once 'Controller/CarrouselCtrl.php';
require_once 'Controller/ParceiroCtrl.php';
require_once 'Secure.php';

use Controller\AlbumCtrl;
use Controller\CarrouselCtrl;
use Controller\CredencialCtrl;
use Controller\DetalheCtrl;
use Controller\EmpresaCtrl;
use Controller\FacebookCtrl;
use Controller\ImagemCtrl;
use Controller\LoginCtrl;
use Controller\PaginaCtrl;
use Controller\ParceiroCtrl;
use Controller\Printer;
use Controller\ProdutoCtrl;
use Controller\SessaoCtrl;
use Slim\Middleware\ContentTypes;
use Slim\Slim;

$secureLogin = (new Secure())->secSessionStart();
$app = new Slim();
$app->add(new ContentTypes());
$printer = new Printer();

$loginCtrl = (new LoginCtrl($printer, $entityManager))->registerRoutes('/login/', $app);

$credencialCtrl = (new CredencialCtrl($printer, $entityManager))->registerRoutes('/credencial/', $app);

$imagemCtrl = (new ImagemCtrl($printer, $entityManager))->registerRoutes('/imagem/', $app);

$albumCtrl = (new AlbumCtrl($printer, $entityManager))->registerRoutes('/album/', $app);

$paginaCtrl = (new PaginaCtrl($printer, $entityManager))->registerRoutes('/pagina/', $app);

$produtoCtrl = (new ProdutoCtrl($printer, $entityManager))->registerRoutes('/produto/', $app);

$sessaoCtrl = (new SessaoCtrl($printer, $entityManager))->registerRoutes('/sessao/', $app);

$detalheCtrl = (new DetalheCtrl($printer, $entityManager))->registerRoutes('/detalhe/', $app);

$facebookCtrl = (new FacebookCtrl($printer, $entityManager))->registerRoutes('/facebook/', $app);

$empresaCtrl = (new EmpresaCtrl($printer, $entityManager))->registerRoutes('/empresa/', $app);

$carrouselCtrl = (new CarrouselCtrl($printer, $entityManager))->registerRoutes('/carrousel/', $app);

$parceiroCtrl = (new ParceiroCtrl($printer, $entityManager))->registerRoutes('/parceiro/', $app);

$app->post('/logout', function () use ($secureLogin, $printer) {
	$secureLogin->secSessionDestroy();
	$printer->printJsonResponse(['msg' => 'unlogged']);
});

function startsWith($haystack, $needle) {
	return substr($haystack, 0, strlen($needle)) === $needle;
}

$app->hook('slim.before.dispatch', function () use($app, $loginCtrl, $printer, $secureLogin) {
	$secureLogin->secSessionStart();
	$path = $app->request()->getPathInfo();

	if (!startsWith($path, "/login") && !startsWith($path, "/logout")) {
		if (!$loginCtrl->loginCheck()) {
			$var = $printer->printJsonResponse(['logged' => false], false);
			$app->halt(401, $var);
			exit();
		}
	}
});
$app->run();
