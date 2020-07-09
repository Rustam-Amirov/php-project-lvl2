<?php

namespace Differ\Parsers;

use Symfony\Component\Yaml\Yaml;

function parse($data, $format)
{
    switch ($format) {
        case 'json':
            return json_decode($data, false);
        break;
        case 'yaml':
        case 'yml':
            return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
        break;
        default:
            throw new \Exception("Неподдерживаемый формат файла: {$format}", 1);
    }
}
