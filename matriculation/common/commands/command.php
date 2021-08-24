<?php
namespace matriculation\common;

use GuzzleHttp\Promise\Promise;

interface CommandInterface {
    public function getClassName(): string;
}

interface CommandHandlerInterface {
    public function handle(CommandInterface $command): Promise;
}

abstract class CommandAbstract implements CommandInterface {
    private string $className;

    function __construct(string $className) {
        $this->className = $className;
    }

    public function getClassName(): string {
        return $this->className;
    }
}
?>