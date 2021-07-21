<?php

namespace Differ\Formatters\Json;

function getJsonFormat(array $data): string
{
    $json = json_encode($data);
    if ($json === false) {
        throw new \Exception("Can't convert to json");
    }
    return $json;
}
