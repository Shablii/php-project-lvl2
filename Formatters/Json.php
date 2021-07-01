<?php

namespace Differ\Formatters\Json;

function json($ast)
{
    return collect($ast)
    ->toJson();
}
