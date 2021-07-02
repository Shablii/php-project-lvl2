<?php

namespace Differ\Formatters\Json;

function json(object $ast): string
{
    return collect($ast)->toJson();
}
