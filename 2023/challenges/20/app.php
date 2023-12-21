<?php

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

define('DEBUG', true);
define('INPUT_FILE', __DIR__ . '/example_input.txt');

enum Pulse: int
{
    case High = 1;
    case Low = 0;
}

enum ModuleType: string
{
    case Broadcast = 'broadcaster';
    case Conjunction = '&';
    case FlipFlop = '%';
}

abstract class Module
{
    private array $inputs = [];
    private array $outputs = [];

    private array $send = [];

    public function __construct(
        readonly private string $name,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function addInput(Module $module)
    {
        $this->inputs[$module->name()] = $module;
    }

    public function addOutput(Module $module)
    {
        $this->outputs[$module->name()] = $module;
    }

    public function receive(Module $sender, Pulse $pulse)
    {
        if (!isset($this->inputs[$sender->name()])) {
            $this->inputs[$sender->name()] = $sender;
        }
    }

    protected function send(Pulse $pulse)
    {
        foreach ($this->outputs as $module) {
            $pulseString = $pulse === Pulse::Low ? '-low->' : '-high->';
            printf('%s %s %s' . PHP_EOL, $this->name(), $pulseString, $module->name());
            $module->receive($this, $pulse);
            $this->sent[] = $pulse;
        }
    }
}

class Button extends Module
{
    public function push()
    {
        $this->send(Pulse::Low);
    }
}

class Broadcast extends Module
{
    public function receive(Module $sender, Pulse $pulse)
    {
        parent::receive($sender, $pulse);

        $this->send($pulse);
    }
}

class Conjunction extends Module
{
    private array $states = [];

    public function addInput(Module $module)
    {
        parent::addInput($module);

        $this->states[$module->name()] = Pulse::Low;
    }

    public function receive(Module $sender, Pulse $pulse)
    {
        parent::receive($sender, $pulse);

        $this->states[$sender->name()] = $pulse;

        // Check if all pulses are high
        if (count(array_filter($this->states, fn($state) => $state === Pulse::Low)) === 0) {
            $this->send(Pulse::Low);

            return;
        }

        $this->send(Pulse::High);
    }
}

class FlipFlop extends Module
{
    private bool $state = false;

    public function receive(Module $sender, Pulse $pulse)
    {
        parent::receive($sender, $pulse);

        if ($pulse === Pulse::Low) {
            $this->state = !$this->state;
        }

        if ($this->state) {
            $this->send(Pulse::High);
            $this->state = !$this->state;

            return;
        }

        $this->send(Pulse::Low);
    }
}

$input = new SplFileObject(INPUT_FILE);
$modules = [];
while (!$input->eof() && $line = $input->fgets()) {
    $line = trim($line);

    preg_match('/(?P<module_type>%|&)?(?P<module_name>[a-z]+) -> (?P<outputs>.*)/', $line, $matches);

    [
        'module_type' => $moduleType,
        'module_name' => $moduleName,
        'outputs' => $outputs,
    ] = $matches;

    $moduleType = !empty($moduleType) ? $moduleType : $moduleName;
    $outputs = explode(',', str_replace(' ', '', $outputs));

    $modules[$moduleName]['module'] = match ($moduleType) {
        ModuleType::Broadcast->value => new Broadcast($moduleName),
        ModuleType::Conjunction->value => new Conjunction($moduleName),
        ModuleType::FlipFlop->value => new FlipFlop($moduleName),
    };

    $modules[$moduleName]['outputs'] = $outputs;
}

foreach ($modules as $name => $module) {
    foreach ($module['outputs'] as $output) {
        $module['module']->addOutput($modules[$output]['module']);
        $modules[$output]['module']->addInput($module['module']);
    }
}

$button = new Button('button');
$button->addOutput($modules['broadcaster']['module']);
$modules['broadcaster']['module']->addInput($button);

$button->push();
