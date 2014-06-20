<?php

namespace Pourquoi\InfluxDB\Sender;

class EchoSender implements SenderInterface
{
	/**
	 * {@inheritDoc}
	 */
	public function open($db) {
		return null;
	}

	/**
	 * {@inheritDoc}
	 */
	public function write($handle, $data) {
		$data = json_encode($data, JSON_PRETTY_PRINT);
		echo "$data";

		return strlen($data);
	}

	/**
	 * {@inheritDoc}
	 */
	public function close($handle) {

	}
}