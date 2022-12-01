<?php

namespace Tests\Artifacts;

use Bluestone\DataTransferObject\Casters\Caster;
use NumberFormatter;

class AmountCaster implements Caster
{
    public function cast(mixed $value): float
    {
        $formatter = NumberFormatter::create('fr', NumberFormatter::CURRENCY);

        return $formatter->parseCurrency($value, $currency);
    }
}
