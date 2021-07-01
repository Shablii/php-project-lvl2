<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parsers($file): object
{
    //var_dump(file_get_contents($file));
    $typeFile = strpbrk($file, ".");

    $parsFiles = $typeFile === ".json" ?
        json_decode(file_get_contents($file), false) :
        Yaml::parse(file_get_contents($file), Yaml::PARSE_OBJECT_FOR_MAP);

    return $parsFiles;
}
