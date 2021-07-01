<?php

namespace Differ\Formatters\Json;

function json($ast): string
{
    return collect($ast)
    ->toJson();
}
