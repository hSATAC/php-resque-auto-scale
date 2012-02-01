<?php
require_once 'php-resque/lib/Resque.php';
require_once 'php-resque/lib/Resque/Worker.php';
Resque_Event::listen('afterEnqueue', array('Resque_Scaler', 'afterEnqueue'));
Resque_Event::listen('afterPerform', array('Resque_Scaler', 'afterPerform'));

class Resque_Scaler
{
    // define how many jobs require how many workers.
    /*public static $SCALE_SETTING = array(
        15 => 2,
        25 => 3,
        40 => 4,
        60 => 5
    );*/
    public static $SCALE_SETTING = array(
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5
    );

	public static function afterEnqueue($class, $arguments)
	{
		echo "Job was queued for " . $class . ".\n";
        $class_vars = get_class_vars($class);
        //$job_count = Resque::size($class_vars["queue"]);

        //echo $job_count ." jobs is pending in queue ". $class_vars["queue"] . "\n";

        if(self::check_need_worker($class_vars["queue"])) {
            echo "we need more workers\n";
            self::add_worker();
        } else {
            echo "workers is enough.\n";
        }

	}

	public static function afterPerform($job)
	{
		echo "Just performed " . $job . "\n";
    }

    // -----------------
    public static function cal_need_worker($queue)
    {
        $need_worker = 1;
        $pending_job_count = Resque::size($queue);
        echo "pending:{$pending_job_count}\n";
        // check if we need more workers
        foreach(self::$SCALE_SETTING as $job_count => $worker_count) {
            if($pending_job_count > $job_count) {
                echo $pending_job_count." > " . $job_count . "\n";
                $need_worker = $worker_count;
            }
        }

        return $need_worker;
    }

    public static function check_need_worker($queue)
    {
        $need_worker = self::cal_need_worker($queue);
        $current_worker = sizeof(self::get_all_workers($queue));

        echo "need:".$need_worker."|current:".$current_worker."\n";
        return ($need_worker > $current_worker) ? TRUE : FALSE;
    }
	


    // get worker info directly from redis, bad practice.
    // TODO: refactor with a Resque_Scaler_Worker extends Resque_Worker
    public static function get_all_workers($queue=NULL) 
    {
        $ret = array();

        $workers = Resque::redis()->smembers('workers');
        if(!is_array($workers)) {
            $workers = array();
        }
        foreach($workers as $workerId) {
            $worker_data = explode(':', $workerId, 3);

            $worker = array();
            $worker['hostname'] = $worker_data[0];
            $worker['queues'] = explode(',', $worker_data[2]);
            $worker['pid'] = $worker_data[1];

            if(($queue && (in_array($queue, $worker['queues']) || in_array("*", $worker['queues']))) || !$queue) 
            {
                $ret[] = $worker;
            }
        }

        return $ret;
    }

    public static function set_backend()
    {
        Resque::setBackend("localhost:6379");
    }

    public static function server_workers($workers=array()) 
    {
        $ret = array();
        foreach($workers as $worker) {
            $ret[$worker['hostname']][] = $worker;
        }

        return $ret;
    }

    public static function get_hostname()
    {
          if(function_exists('gethostname')) {
              $hostname = gethostname();
          }
          else {
              $hostname = php_uname('n');
          }

          return $hostname;
    }

    public static function add_worker()
    {
        $server_workers = self::server_workers(self::get_all_workers());
        $current_workers = $server_workers[self::get_hostname()];
        if(sizeof($current_workers) > 0) {
            $pid = pcntl_fork();
            if($pid == -1) {
                die("Could not fork worker ".$i."\n");
            }
            // Child, start the worker
            else if(!$pid) {

                // if there are more than 1 types of workers on this machine, we don't know which kind to create. just create the first one.
                $worker = new Resque_Worker($current_workers[0]['queues']);
                // TODO: set logLevel
                fwrite(STDOUT, '*** Starting worker '.$worker."\n");
                // TODO: set interval
                $worker->work();
            }

            return TRUE;
        }

        return FALSE;
    }
}
