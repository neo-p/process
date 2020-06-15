<?php

namespace NeoP\Process;

use Swoole\Process as SwooleProcess;
use NeoP\DI\Annotation\Mapping\Depend;
use NeoP\DI\DependType;

/**
 * @Depend(type=DependType::FACTORY)
 */
class Process
{


    private $process;

    const MASTER = 'master';
    const MANAGER = 'manager';
    const WORKER = 'worker';
    const PROCESSOR = 'php';

    public function create(callable $function, bool $redirectIO = false, int $pipeType = SOCK_DGRAM, bool $enableCo = false)
    {
        $this->process = new SwooleProcess($function, $redirectIO, $pipeType, $enableCo);
    }
    
    public function start()
    {
        $this->process->start();
    }

    public function name($role)
    {
        $this->process->name($this->getProcessName($role));
    }

    public function daemon(bool $nochdir = true, bool $noclose = true)
    {
        $this->process->daemon($nochdir, $noclose);
    }

    public function wait(bool $blocking  = true)
    {
        $this->process->wait($blocking);
    }

    public function getPid()
    {
        return $this->process->pid;
    }

    public function exit(int $status = 0)
    {
        return $this->process->exit($status);
    }

    public static function getProcessName(string $process)
    {
        return self::PROCESSOR . ' ' . service("server.name", "neo-p") . ' ' . $process;
    }

    public static function kill(int $pid, int $signo = SIGTERM): bool
    {
        return SwooleProcess::kill($pid, $signo);
    }
}
