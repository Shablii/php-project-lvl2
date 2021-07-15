<?php

namespace Differ\Formatters\Json;

function json($ast): string
{
    return json_encode($ast);
}
