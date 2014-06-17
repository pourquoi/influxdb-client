<?php

namespace Pourquoi\InfluxDB;

use Pourquoi\InfluxDB\Sender\CurlSender;
use Pourquoi\InfluxDB\Sender\SenderInterface;

class Client
{
	private $host;
	private $port;

	private $queue;

	private $sender_map;
	private $default_sender;

	public function __construct($host='127.0.0.1', $port='8086', $options=array()) {
		$this->port = $port;
		$this->host = $host;

		$defaults = array(
			'base_url' => '',
			'user' => 'root',
			'password' => 'root'
		);

		$options = array_merge($defaults, $options);

		$this->default_sender = new CurlSender($host, $port, $options['user'], $options['password'], $options['base_url']);
		$this->sender_map = array();
		$this->queue = array();
	}

	public function setDBSender($db, SenderInterface $sender) {
		$this->sender_map[$db] = $sender;
	}

	public function setDefaultSender(SenderInterface $sender) {
		$this->default_sender = $sender;
	}

	public function getSender($db) {
		return isset($this->sender_map[$db]) ? $this->sender_map[$db] : $this->default_sender;
	}

	public function insert($db, $serie, $columns='value', $points=1) {
		if( !is_array($points) ) $points = array(array($points));
		else if( !is_array($points[0]) ) $points = array($points);

		if( !is_array($columns) ) $columns = array($columns);

		$data = array(
			'name' => $serie,
			'columns' => $columns,
			'points' => $points
		);

		$this->queue[$db][] = $data;
	}

	public function send($db=null) {
		$queue = $db ? array($db=>$this->queue[$db]) : $this->queue;

		$w = 0;
		foreach( $queue as $db=>$data) {
			$sender = $this->getSender($db);
			$h = $sender->open($db);
			$w += $sender->write($h, $data);
			$sender->close($h);

			unset($this->queue[$db]);
		}

		return $w;
	}
}