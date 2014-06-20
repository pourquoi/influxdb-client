<?php

namespace Pourquoi\InfluxDB\Sender;

class UDPSender implements SenderInterface
{
	private $host;
	private $port;

	public function __construct($host, $port, $options=array()) {
		$this->host = $host;
		$this->port = $port;
	}

	/**
	 * {@inheritDoc}
	 */
	public function open($db) {
		$s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_connect($s, $this->host, $this->port);

		return $s;
	}

	/**
	 * {@inheritDoc}
	 */
	public function write($handle, $data) {
		$msg = json_encode($data);

		return socket_write($handle, $msg, strlen($msg));
	}

	/**
	 * {@inheritDoc}
	 */
	public function close($handle) {
		socket_close($handle);
	}
}