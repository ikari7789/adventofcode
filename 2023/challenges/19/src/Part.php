<?php

declare(strict_types=1);

class Part implements Stringable
{
    public function __construct(
        readonly private int $cool,
        readonly private int $musical,
        readonly private int $aerodynamic,
        readonly private int $shiny,
    ) {}

    public function cool(): int
    {
        return $this->cool;
    }

    public function musical(): int
    {
        return $this->musical;
    }

    public function aerodynamic(): int
    {
        return $this->aerodynamic;
    }

    public function shiny(): int
    {
        return $this->shiny;
    }

    public function sum(): int
    {
        return $this->cool() + $this->musical() + $this->aerodynamic() + $this->shiny();
    }

    public function __toString(): string
    {
        $string = '';

        $string .= '{';
        $string .= 'x=' . $this->cool();
        $string .= ',';
        $string .= 'm=' . $this->musical();
        $string .= ',';
        $string .= 'a=' . $this->aerodynamic();
        $string .= ',';
        $string .= 's=' . $this->shiny();
        $string .= '}';

        return $string;
    }
}
