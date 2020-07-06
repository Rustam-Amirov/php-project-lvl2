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
    $data = extract($filePath);
    $format = pathinfo($filePath, PATHINFO_EXTENSION);
    return render($data, $format);
}


function extract($filePath)
{
    return file_get_contents(realpath($filePath));
}


function render($data, $format)
{
    switch ($format) {
        case 'json':
            return json_decode($data, false);
        break;
        case 'yaml':
            return Yaml::parse($data, Yaml::PARSE_OBJECT_FOR_MAP);
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
            $iter = buildNode($key, 'deleted', $tree1->$key);
        } elseif (!property_exists($tree1, $key)) {
            $iter = buildNode($key, 'added', null, $tree2->$key);
        } else {
            if (is_object($tree1->$key) && is_object($tree2->$key)) {
                $iter = buildNode($key, 'nested', $tree1->$key, $tree2->$key, diff($tree1->$key, $tree2->$key));
            } elseif ($tree1->$key === $tree2->$key) {
                $iter = buildNode($key, 'unchanged', $tree1->$key, $tree2->$key);
            } else {
                $iter = buildNode($key, 'changed', $tree1->$key, $tree2->$key);
            }
        }
        return $iter;
    }, $keys);
}


function buildNode($key, $type, $oldValue = null, $newValue = null, $children = [])
{
    return ["key" => $key, "children" => $children, "oldValue" => $oldValue, "newValue" => $newValue, "type" => $type];
}
