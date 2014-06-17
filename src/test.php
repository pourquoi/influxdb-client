<?php

require dirname(__FILE__).'/../vendor/autoload.php';

$client = new \Pourquoi\InfluxDB\Client('127.0.0.1', '8086');

$client->setDBSender('famihero', new \Pourquoi\InfluxDB\Sender\UDPSender('127.0.0.1', '4444'));
//$client->setDBSender('famihero', new \Pourquoi\InfluxDB\Sender\EchoSender());

$client->insert('famihero', 'test_oo', array('foo', 'val'), array('mathias', 7));
$client->send();