<?php
Resque_Event::listen('afterEnqueue', array('Resque_Auto_Scaler', 'afterEnqueue'));
Resque_Event::listen('afterPerform', array('Resque_Auto_Scaler', 'afterPerform'));

class Resque_Auto_Scaler
{
    // define how many jobs require how many workers.
    public static $SCALE_SETTING = array(
        15 => 2,
        25 => 3,
        40 => 4,
        60 => 5
    );
	public static function afterEnqueue($class, $arguments)
	{
		echo "Job was queued for " . $class . ". Arguments:\n";
        $class_vars = get_class_vars($class);
        $job_count = Resque::size($class_vars["queue"]);

        echo $job_count ." jobs is pending in queue ". $class_vars["queue"] . "\n";
	}
	
	
	public static function afterPerform($job)
	{
		echo "Just performed " . $job . "\n";
	}
}
