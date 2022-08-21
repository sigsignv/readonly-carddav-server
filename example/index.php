<?php
/**
 * CardDAV server example
 */
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

$baseUri = '/';
$dir = dir(__DIR__ . '/data');

// Backends
$authBackend = new Cross\AuthBackend();
$principalBackend = new Cross\PrincipalBackend();
$carddavBackend = new Cross\CardDAVBackend\StaticFile($dir);

// Direcotry tree
$nodes = [
    new Sabre\DAVACL\PrincipalCollection($principalBackend),
    new Sabre\CardDAV\AddressBookRoot($principalBackend, $carddavBackend),
];

$server = new Sabre\DAV\Server($nodes);
$server->setBaseUri($baseUri);

// Plugins
$server->addPlugin(new Sabre\DAV\Auth\Plugin($authBackend));
$server->addPlugin(new Sabre\DAVACL\Plugin());
$server->addPlugin(new Sabre\CardDAV\Plugin());
//$server->addPlugin(new Sabre\DAV\Browser\Plugin());

$server->start();
