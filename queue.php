<?php
if(empty($argv[1])) {
  $argv[1] = 'PHP_Job';
}

require 'php-resque/lib/Resque.php';
require 'job.php';
require 'plugin.php';
date_default_timezone_set('GMT');
Resque::setBackend('127.0.0.1:6379');

$args = array(
	'time' => time(),
	'array' => array(
		'test' => 'test',
	),
);
$class_vars = get_class_vars($argv[1]);
$jobId = Resque::enqueue($class_vars['queue'], $argv[1], $args, true);
echo "Queued job ".$jobId."\n\n";
?>
