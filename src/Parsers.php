<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parsers($file)
{
    $typeFile = strpbrk($file, ".");
    $parsFiles = $typeFile === ".json" ? json_decode(file_get_contents($file), true) : Yaml::parse(file_get_contents($file), Yaml::PARSE_OBJECT_FOR_MAP);

    return collect($parsFiles)
    ->map(function ($v, $k) {
        if (is_bool($v)) {
            $v = (true === $v) ? "true" : "false";
        }
        return $v;
    })->all();
}
