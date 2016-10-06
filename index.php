<?php
    require_once __DIR__ . '/vendor/autoload.php';

    use \Database\PostgressDb;
    use \Controller\WtfController;
    use \Repository\WtfRepository;

    $str = file_get_contents('config/parameters.json');
    $json = json_decode($str, true);

    $database = new PostgressDb($json['host'], $json['port'], $json['dbName'], $json['usernameName'], $json['userPassword']);
    $database->connect();

    $wtfRepository = new \Repository\WtfRepository();
    $controller = new \Controller\WtfController($database, $wtfRepository);
    $controller->getAllWtfPointsAction();

    $database->disconnect();