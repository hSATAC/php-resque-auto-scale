<?php
if(empty($argv[1])) {
  $argv[1] = 'PHP_Job';
}

require 'php-resque/lib/Resque.php';
require 'plugin.php';
date_default_timezone_set('GMT');
Resque::setBackend('127.0.0.1:6379');

$args = array(
	'time' => time(),
	'array' => array(
		'test' => 'test',
	),
);

$jobId = Resque::enqueue('default', $argv[1], $args, true);
echo "Queued job ".$jobId."\n\n";
?>
