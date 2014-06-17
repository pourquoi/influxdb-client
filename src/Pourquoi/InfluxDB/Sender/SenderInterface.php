<?php

namespace Pourquoi\InfluxDB\Sender;

interface SenderInterface
{
	function open($db);

	function write($handle, $data);

	function close($handle);
}