<?php

namespace Pourquoi\InfluxDB\Sender;

interface SenderInterface
{
	/**
	 * @param string $db database name
	 *
	 * @return mixed false on error, resource on success or null if the sender does not need resource
	 */
	function open($db);

	/**
	 * @param resource $handle the resource returned by open
	 * @param array $data
	 *
	 * @return mixed
	 */
	function write($handle, $data);

	/**
	 * @param resource $handle
	 */
	function close($handle);
}