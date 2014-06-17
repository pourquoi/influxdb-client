Send data to influxdb via UDP

```php
$client = new \Pourquoi\InfluxDB\Client();
$client->setDBSender('my_db', new \Pourquoi\InfluxDB\Sender\UDPSender('127.0.0.1', '4444'));

$client->insert('my_db', 'foo', array('v1', v2'), array('23', '456'));
$client->insert('my_db', 'foo', array('v1', v2'), array('15', '412'));

// this will be sent via http
$client->insert('another_db', 'bar', array('foo'), array('baz'));

$client->send();
```
