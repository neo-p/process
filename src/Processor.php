<?php declare(strict_types=1);

namespace NeoP\Process;

use NeoP\Process\Process;
use NeoP\DI\Annotation\Mapping\Inject;
use NeoP\DI\Annotation\Mapping\Depend;

/**
 * @Depend()
 */
class Processor
{
    /**
     * @Inject()
     * @var Process
     */
    private $process;

    private $pipeType = SOCK_STREAM;

    private $stdIO = FALSE;

    private $tasks = [];

    public static $isDaemon = false;
    
    public function run(callable $callback)
    {
        $this->process->create($callback, $this->stdIO, $this->pipeType, FALSE);
        $this->process->start();
        $this->process->name('root-' . Process::MASTER);
        foreach ($this->tasks as $task) {
            $task();
        }

        if (self::$isDaemon) {
            Process::kill(posix_getpid(), SIGUSR1);
        } else {
            $this->process->wait(true);
        }
    }

    public function setStdIO(bool $stdIO)
    {
        $this->stdIO = $stdIO;
    }

    public function setTask(callable $task)
    {
        $this->tasks[] = $task;
    }

    public function setPipeType(int $pipeType)
    {
        $this->pipeType = $pipeType;
    }

}
