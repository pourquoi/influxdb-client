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

	private $auto_send;

	/**
	 * @param string $host
	 * @param string $port
	 * @param array $options
	 */
	public function __construct($host='127.0.0.1', $port='8086', $options=array()) {
		$this->port = $port;
		$this->host = $host;

		$defaults = array(
			'base_url' => '',
			'user' => 'root',
			'password' => 'root',
			'auto_send' => false
		);

		$options = array_merge($defaults, $options);

		$this->auto_send = $options['auto_send'];

		$this->default_sender = new CurlSender($host, $port, $options['user'], $options['password'], $options['base_url']);
		$this->sender_map = array();
		$this->queue = array();
	}

	/**
	 * Attach a sender to a database.
	 *
	 * @param string $db
	 * @param SenderInterface $sender
	 */
	public function setDBSender($db, SenderInterface $sender) {
		$this->sender_map[$db] = $sender;
	}

	/**
	 * @param SenderInterface $sender
	 */
	public function setDefaultSender(SenderInterface $sender) {
		$this->default_sender = $sender;
	}

	/**
	 * Return the sender used for the db.
	 *
	 * @param string $db
	 *
	 * @return SenderInterface
	 */
	public function getSender($db) {
		return isset($this->sender_map[$db]) ? $this->sender_map[$db] : $this->default_sender;
	}

	/**
	 * Insert data into a serie.
	 * If auto_send options is false(default) the data will be actually sent on next call to send().
	 *
	 * @param string $db
	 * @param string $serie
	 * @param mixed $columns
	 * @param mixed $points
	 */
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

		if( $this->auto_send )
			$this->send($db);
	}

	/**
	 * Send queued data.
	 * If db is null send all queued data, else only send data queued for the db.
	 *
	 * @param string|null $db
	 *
	 * @return int bytes written.
	 */
	public function send($db=null) {
		$queue = $db ? array($db=>$this->queue[$db]) : $this->queue;

		$w = 0;
		foreach( $queue as $db=>$data) {
			$sender = $this->getSender($db);
			$h = $sender->open($db);
			if( $h ) {
				$w += $sender->write($h, $data);
				$sender->close($h);
			}

			unset($this->queue[$db]);
		}

		return $w;
	}
}