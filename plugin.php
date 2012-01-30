<?php
Resque_Event::listen('afterEnqueue', array('Resque_Auto_Scaler', 'afterEnqueue'));
Resque_Event::listen('afterPerform', array('Resque_Auto_Scaler', 'afterPerform'));

class Resque_Auto_Scaler
{
	public static function afterEnqueue($class, $arguments)
	{
		echo "Job was queued for " . $class . ". Arguments:";
		print_r($arguments);
	}
	
	
	public static function afterPerform($job)
	{
		echo "Just performed " . $job . "\n";
	}
}
