<?php

namespace NeoP\Process;

use NeoP\Process\Process;
use NeoP\Process\Processor;
use NeoP\Console\Annotation\Mapping\Command;
use NeoP\Console\Annotation\Mapping\CommandOption;
use NeoP\Console\Annotation\Mapping\CommandMapping;
use NeoP\DI\Annotation\Mapping\Inject;
use NeoP\DI\Container;
use NeoP\Process\Exception\ProcessException;

/**
 * @Command("process", alias="p", describe="Control the process.")
 */
class Commander
{

    /**
     * @Inject()
     * @var Process
     */
    private $process;

    /**
     * @CommandMapping("start", describe="Start process.")
     */
    public function start()
    {
        $service = service('server.service');
        if (! $service) {
            throw new ProcessException("server.service is not found");
        }

        if (Container::hasDefinition($service)) {
            $server = Container::getDefinition($service);
            if (!method_exists($server, "run")) {
                throw new ProcessException("Class " . $service . " not exists function run.");
            }
        } else {
            throw new ProcessException("Service must be add @Depend annotation");
        }
        $server->run($this->process);
    }

    /**
     * @CommandOption("deamon", alias="d", describe="Execute the program as a daemon.")
     */
    public function daemon()
    {
        $processor = Container::getDefinition(Processor::class);
        $process = $this->process;
        $processor->setStdIO(TRUE);
        $processor::$isDaemon = true;
        $processor->setPipeType(SOCK_STREAM);
        $processor->setTask(function () use ($process) {
            $process->daemon(true, true);
        });
    }
}
