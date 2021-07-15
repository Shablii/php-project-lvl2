<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parsers(string $content, string $type): array
{
    switch ($type) {
        case 'json':
            return json_decode($content, true);
        case 'yaml' || 'yml':
            return Yaml::parse($content);
        default:
            throw new \Exception("unknown file format $type");
    }
}
