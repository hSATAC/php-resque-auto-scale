<?php
class PHP_Job
{
    static public $queue = "default";
	public function perform()
	{
		sleep(5);
		fwrite(STDOUT, 'Hello!');
	}
}
?>
