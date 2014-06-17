<?php

namespace Pourquoi\InfluxDB\Sender;

class EchoSender implements SenderInterface
{
	public function open($db) {

	}

	public function write($handle, $data) {
		$data = json_encode($data, JSON_PRETTY_PRINT);
		echo "$data";

		return strlen($data);
	}

	public function close($handle) {

	}
}