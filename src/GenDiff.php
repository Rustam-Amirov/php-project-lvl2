<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Funct\Collection\union;
use function Differ\Formatters\getFormatter;

function genDiff($firstPathToFile, $secondPathToFile, $format = 'pretty')
{
    $firstData = getDataFile($firstPathToFile);
    $secondData = getDataFile($secondPathToFile);
    $formatter = getFormatter($format);
    $diffResult = diff($firstData, $secondData);
    $result = $formatter($diffResult);
    return $result;
}

function getDataFile($filePath)
{
    $format = pathinfo($filePath, PATHINFO_EXTENSION);
    $getData = extract($format);
    return $getData($filePath);
}

function extract($format)
{
    switch ($format) {
        case 'json':
            return function ($filePath) {
                $data = json_decode(file_get_contents(realpath($filePath)), false);
                return $data;
            };
        break;
        case 'yaml':
            return function ($filePath) {
                $data = Yaml::parse(file_get_contents(realpath($filePath)), Yaml::PARSE_OBJECT_FOR_MAP);
                return $data;
            };
        break;
        default:
            throw new \Exception("Неподдерживаемый формат файла: {$format}", 1);
    }
}


function diff($tree1, $tree2)
{
    $keys = union(array_keys(get_object_vars($tree1)), array_keys(get_object_vars($tree2)));
    return  array_map(function ($key) use ($tree1, $tree2) {
        if (!property_exists($tree2, $key)) {
            return buildNode($key, $tree1->$key, null, "deleted");
        } elseif (!property_exists($tree1, $key)) {
            return buildNode($key, null, $tree2->$key, "added");
        } else {
            if (is_object($tree1->$key) && is_object($tree2->$key)) {
                return buildNode($key, $tree1->$key, $tree2->$key, "nested", diff($tree1->$key, $tree2->$key));
            } elseif ($tree1->$key === $tree2->$key) {
                return buildNode($key, $tree1->$key, $tree2->$key, "unchanged");
            } else {
                return buildNode($key, $tree1->$key, $tree2->$key, "changed");
            }
        }
    }, $keys);
}


function buildNode($key, $oldValue, $newValue, $type, $children = [])
{
    return ["key" => $key, "children" => $children, "oldValue" => $oldValue, "newValue" => $newValue, "diff" => $type];
}
