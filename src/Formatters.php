<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\stylish;
use function Differ\Formatters\Plain\plain;

function formatters($ast, $format)
{
    switch ($format) {
        case 'stylish':
            return stylish($ast);
        case 'plain':
            return plain($ast);
        default:
            throw new \Exception("unknown format $format");
    }
}
