<?php

namespace Differ\Formatters\Json;

function json(object $ast): string
{
    //print_r(collect($ast)->toJson());
    return collect($ast)->toJson();
}
