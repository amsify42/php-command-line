<?php

namespace Amsify42\CommandLine;

use Amsify42\CommandLine\CommandLine;
use Amsify42\CommandLine\Helper\ExecTime;
use DateTime;

class Task
{
    private static $taskJsonFile = NULL;

    private static $logPath = NULL;

    private $inst = NULL;

    private $script = NULL;

    private $ran = false;

    private $info = [];

    private $resumed = false;

    function __construct()
    {
        ExecTime::start();
        $this->script = CommandLine::isParam(1) ? CommandLine::getParam(1) : '';
        if (!$this->script) {
            $this->script = CommandLine::isParam('__script') ? CommandLine::getParam('__script') : '';
        }
    }

    public static function setLogPath($logPath=NULL)
    {
        self::$logPath = $logPath;
    }

    public static function setTaskJsonFile($taskJsonFile=NULL)
    {
        self::$taskJsonFile = $taskJsonFile;
    }

    public function setParams($script, $params = [])
    {
        CommandLine::setParams($params);
        $this->script = $script;
        $this->resumed = true;
        return $this;
    }

    public function process()
    {
        if ($this->script) {
            if ($this->resumed === false) {
                printMsg('*** Started Task:' . $this->script . ': ' . date('Y-m-d H:i:s') . ' ***', false);
            }

            if ($this->checkUnique()) {
                $this->ran = true;
                $taskFile  = $this->script;

                $isOnLoop    = CommandLine::isParam('onLoop');
                $onLoopSleep = CommandLine::getParam('onLoopSleep');
                $onLoopSleep = ($onLoopSleep) ? $onLoopSleep : 3600;
                if ($isOnLoop) {
                    printMsg('Starting on Loop');
                    $loopNo = 1;
                    while (true) {
                        printMsg('Loop No:' . $loopNo);
                        $startTime = new DateTime();
                        $this->inst = new $taskFile();
                        $this->inst->init();
                        printMsg('Loop Execution Time: ' . ExecTime::ended(false, $startTime), false);
                        printMsg('Sleeping for seconds:' . $onLoopSleep);
                        sleep($onLoopSleep);
                        $loopNo++;
                    }
                } else {
                    $this->inst = new $taskFile();
                    $this->inst->init();
                }
            } else {
                printMsg('The task is already running');
            }
        } else {
            printMsg('Name param is required');
        }
    }

    private function checkUnique()
    {
        if (CommandLine::isParam('isUnique')) {
            $taskJsonFileData = file_get_contents(self::$taskJsonFile);
            $fData = ($taskJsonFileData)? json_decode($taskJsonFileData, true): NULL;
            $fData = ($fData && is_array($fData)) ? $fData : [];
            if (isset($fData[$this->script]) && isset($fData[$this->script]['status']) && $fData[$this->script]['status'] == 'running') {
                return false;
            } else {
                $this->info = [
                    'status' => 'running',
                    'started_at' => date('Y-m-d H:i:s'),
                    'completed_at' => null,
                    'time_taken' => null,
                ];
                $fData[$this->script] = $this->info;
                file_put_contents(self::$taskJsonFile, json_encode($fData));
                return true;
            }
        } else {
            return true;
        }
    }

    public static function run($script, $params = [], $isBackground = false, $noLog = false, $forceBackground = false, $beforeExec = NULL, $isDirect = false)
    {
        if ($forceBackground === false && php_sapi_name() == 'cli') {
            if ($isDirect === false) {
                printMsg("Continued CLI Task: {$script}");
            }
            $task = new self();
            $task->setParams($script, $params);

            $paramsStr = CommandLine::toString();
            if ($paramsStr) {
                printMsg("Requested Params:\n-> " . $paramsStr, false, false);
            }

            $task->process();
            return true;
        }

        $isWindows = (substr(php_uname(), 0, 7) == "Windows") ? true : false;
        $sameLog = (isset($params['sameLog']) && $params['sameLog']) ? true : false;
        $overrideLog = (isset($params['overrideLog']) && $params['overrideLog']) ? true : false;
        $globalPath = realpath(__DIR__ . DS . 'Helper' . DS);
        $logPath = ($noLog === true) ? '' : self::$logPath . DS . pathinfo(strtolower($script), PATHINFO_FILENAME);
        $command = ($isWindows ? "" : "cd " . $globalPath . "; ") . (($isBackground && $isWindows === false) ? 'nohup' : '') . " php " . $globalPath . DS . "runcli.php -__script=" . $script;

        if (isset($params['isUnique']) && $params['isUnique']) {
            $taskJsonFileData = file_get_contents(self::$taskJsonFile);
            $fData = ($taskJsonFileData)? json_decode($taskJsonFileData, true): NULL;
            $fData = ($fData && is_array($fData)) ? $fData : [];
            if (isset($fData[$script]) && isset($fData[$script]['status']) && $fData[$script]['status'] == 'running') {
                return false;
            }
        }

        if (sizeof($params) > 0) {
            foreach ($params as $pName => $pValue) {
                if ($pValue === NULL) {
                    $command .= " -" . $pName . " NULL";
                } else if (is_bool($pValue)) {
                    $command .= " --" . $pName;
                } else {
                    if ($logPath && $overrideLog === false && $sameLog === false && in_array($pName, ['u', 'uid', 'userId'])) {
                        $logPath .= '_' . $pValue;
                    }
                    $command .= ' -' . $pName . ' "' . $pValue . '"';
                }
            }
        }

        if ($isBackground === false) {
            $command .= " --result";
        }

        if ($logPath && $overrideLog === false && $sameLog === false) {
            $logPath .= '_' . time();
        }

        if ($logPath) {
            $logPath .= '.log';
        }

        if ($isBackground) {
            $command .= " >" . ($sameLog ? ">" : "") . " " . (($logPath) ? $logPath : ($isWindows? 'NUL': '/dev/null')) . " 2>&1 & echo $!";
        }

        if ($beforeExec !== NULL) {
            $beforeExec($command, $logPath);
        }

        return exec($command);
    }

    function __destruct()
    {
        $completedAt = date('Y-m-d H:i:s');
        $execTime = ExecTime::ended();
        if (CommandLine::isParam('isUnique') && $this->ran) {
            $this->info['status'] = 'completed';
            $this->info['completed_at'] = $completedAt;
            $this->info['time_taken'] = $execTime;

            $taskJsonFileData = file_get_contents(self::$taskJsonFile);
            $fData = ($taskJsonFileData)? json_decode($taskJsonFileData, true): NULL;
            $fData = ($fData && is_array($fData)) ? $fData : [];
            $fData[$this->script] = $this->info;
            file_put_contents(self::$taskJsonFile, json_encode($fData));
        }
        if ($this->resumed === false) {
            printMsg('*** Completed:' . $this->script . ': ' . $completedAt . ' ***', false);
            printMsg('Execution Time: ' . $execTime, false);
        }
        if (CommandLine::isParam('result') && $this->inst) {
            if (method_exists($this->inst, 'result') && is_callable(array($this->inst, 'result'))) {
                echo "\n" . $this->inst->result();
            }
        }
    }
}
