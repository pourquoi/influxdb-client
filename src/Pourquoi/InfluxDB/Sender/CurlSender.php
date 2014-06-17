<?php

namespace Pourquoi\InfluxDB\Sender;

class CurlSender implements SenderInterface
{
	private $host;
	private $port;
	private $user;
	private $password;
	private $base_url;

	public function __construct($host, $port, $user, $password, $base_url) {
		$this->port = $port;
		$this->host = $host;
		$this->password = $password;
		$this->user = $user;
	}

	public function open($db) {
		$url = "http://{$this->host}:{$this->port}/{$this->base_url}db/{$db}/series";
		$url .= '?' . http_build_query(array('u'=>$this->user, 'p'=>$this->password));

		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_POST, true);

		return $ch;
	}

	public function write($handle, $data) {
		curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($data));
		curl_exec($handle);

		if( !curl_errno($handle) ) {
			return curl_getinfo($handle, CURLINFO_SIZE_UPLOAD);
		}

		return 0;
	}

	public function close($handle) {
		curl_close($handle);
	}
}