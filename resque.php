<?php
date_default_timezone_set('GMT');
require_once 'php-resque/lib/Resque.php';
//require_once 'lib/Resque/Worker.php';

require 'job.php';
require 'plugin.php';
require 'php-resque/resque.php';
?>
