<?php

namespace Pourquoi\InfluxDB\Sender;

class UDPSender implements SenderInterface
{
	private $host;
	private $port;

	public function __construct($host, $port) {
		$this->host = $host;
		$this->port = $port;
	}

	public function open($db) {
		$s = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
		socket_connect($s, $this->host, $this->port);

		return $s;
	}

	public function write($handle, $data) {
		$msg = json_encode($data);

		return socket_write($handle, $msg, strlen($msg));
	}

	public function close($handle) {
		socket_close($handle);
	}
}