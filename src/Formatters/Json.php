<?php

namespace Differ\Formatters\Json;

function json(array $ast): string
{
    $json = json_encode($ast);
    if ($json === false) {
        throw new \Exception("Can't convert to json");
    }
    return $json;
}
