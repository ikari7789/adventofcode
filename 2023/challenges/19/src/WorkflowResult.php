<?php

declare(strict_types=1);

enum WorkflowResult: string
{
    case Accept = 'A';
    case Reject = 'R';
}
