<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parsers(string $file): object
{
    $typeFile = strpbrk($file, ".");

    $parsFiles = $typeFile === ".json" ?
        json_decode(file_get_contents($file), false) :
        Yaml::parse(file_get_contents($file), Yaml::PARSE_OBJECT_FOR_MAP);

    return $parsFiles;
}
