<?php

declare(strict_types=1);

require_once 'Part.php';
require_once 'PartType.php';
require_once 'Workflow.php';
require_once 'WorkflowResult.php';

class Workflows implements Stringable
{
    private array $workflows;

    public function addWorkflow(Workflow $workflow): void
    {
        $this->workflows[$workflow->name()] = $workflow;
    }

    public function calculateAcceptable(string $workflowName, array $ranges)
    {
        printf(
            '%s [%d,%d],[%d,%d],[%d,%d],[%d,%d]' . PHP_EOL,
            $workflowName,
            $ranges[PartType::Cool->value][0],
            $ranges[PartType::Cool->value][1],
            $ranges[PartType::Musical->value][0],
            $ranges[PartType::Musical->value][1],
            $ranges[PartType::Aerodynamic->value][0],
            $ranges[PartType::Aerodynamic->value][1],
            $ranges[PartType::Shiny->value][0],
            $ranges[PartType::Shiny->value][1],
        );

        foreach ($this->workflows[$workflowName]->rules() as $rule) {
            if ($rule->comparator() === Comparator::LessThan) {
                $ranges[$rule->partType()->value][1] = $rule->value() - 1;
            } elseif ($rule->comparator() === Comparator::GreaterThan) {
                $ranges[$rule->partType()->value][0] = $rule->value() + 1;
            }
        }
    }

    public function process(Part $part): WorkflowResult
    {
        // dump($part);

        return $this->processPartInWorkflow($part, $this->workflows['in']);
    }

    private function processPartInWorkflow(Part $part, Workflow $workflow): WorkflowResult
    {
        // dump($workflow->name());

        foreach ($workflow->rules() as $rule) {
            $value = match ($rule->partType()) {
                PartType::Cool => $part->cool(),
                PartType::Musical => $part->musical(),
                PartType::Aerodynamic => $part->aerodynamic(),
                PartType::Shiny => $part->shiny(),
            };

            // dump("return {$value} {$rule->comparator()->value} {$rule->value()};");
            $workflowCondition = eval("return {$value} {$rule->comparator()->value} {$rule->value()};");

            if ($workflowCondition) {
                return match ($rule->fallback()) {
                    WorkflowResult::Accept->value => WorkflowResult::Accept,
                    WorkflowResult::Reject->value => WorkflowResult::Reject,
                    default => $this->processPartInWorkflow($part, $this->workflows[$rule->fallback()]),
                };
            }
        }

        return match ($workflow->fallback()) {
            WorkflowResult::Accept->value => WorkflowResult::Accept,
            WorkflowResult::Reject->value => WorkflowResult::Reject,
            default => $this->processPartInWorkflow($part, $this->workflows[$workflow->fallback()]),
        };
    }

    public function __toString(): string
    {
        $string = '';

        $string .= implode(PHP_EOL, $this->workflows);

        return $string;
    }
}
