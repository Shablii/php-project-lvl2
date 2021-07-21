<?php

namespace Differ\Formatters;

use function Differ\Formatters\Stylish\getStylishFormat;
use function Differ\Formatters\Plain\getPlainFormat;
use function Differ\Formatters\Json\getJsonFormat;

function formatters(array $data, string $format): string
{
    switch ($format) {
        case 'stylish':
            return getStylishFormat($data);
        case 'plain':
            return getPlainFormat($data);
        case 'json':
            return getJsonFormat($data);
        default:
            throw new \Exception("unknown format {$format}");
    }
}
