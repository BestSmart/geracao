<?php

// bootstrap.php


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;

require_once "vendor/autoload.php";
require_once "Configs.php";

$user = constant('dbUsername');
$pass = constant('dbPassword');

$dbh = new PDO('mysql:host='.constant('dbHostName').';dbname='.constant('dbName'), $user, $pass, array(
	PDO::ATTR_PERSISTENT => true
		));

// Create a simple "default" Doctrine ORM configuration for Annotations
$isDevMode = false;
$config = Setup::createAnnotationMetadataConfiguration(array(__DIR__ . "/Model"), $isDevMode);
// or if you prefer yaml or XML
//$config = Setup::createXMLMetadataConfiguration(array(__DIR__."/config/xml"), $isDevMode);
//$config = Setup::createYAMLMetadataConfiguration(array(__DIR__."/config/yaml"), $isDevMode);
// database configuration parameters
$conn = array(
	'dbname' => constant('dbName'),
	'user' => $user,
	'password' => $pass,
	'host' => constant('dbHostName'),
	'driver' => 'pdo_mysql',
	'pdo' => $dbh,
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

function full_path() {
	$s = &$_SERVER;
	$ssl = (!empty($s['HTTPS']) && $s['HTTPS'] == 'on') ? true : false;
	$sp = strtolower($s['SERVER_PROTOCOL']);
	$protocol = substr($sp, 0, strpos($sp, '/')) . (($ssl) ? 's' : '');
	$port = $s['SERVER_PORT'];
	$port = ((!$ssl && $port == '80') || ($ssl && $port == '443')) ? '' : ':' . $port;
	$host = isset($s['HTTP_X_FORWARDED_HOST']) ? $s['HTTP_X_FORWARDED_HOST'] : (isset($s['HTTP_HOST']) ? $s['HTTP_HOST'] : null);
	$host = isset($host) ? $host : $s['SERVER_NAME'] . $port;
	$uri = $protocol . '://' . $host . $s['REQUEST_URI'];
	$segments = explode('?', $uri, 2);
	$url = $segments[0];
	return $url;
}
