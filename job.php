<?php
class PHP_Job
{
	public function perform()
	{
		sleep(5);
		fwrite(STDOUT, 'Hello!');
	}
}
?>
