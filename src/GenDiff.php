<?php

namespace Differ\GenDiff;

use function Funct\Collection\union;
use function Differ\Gendiff\Formatters\getFormatter;
use function Differ\Parsers\parse;

function genDiff($firstPathToFile, $secondPathToFile, $format = 'pretty')
{
    $firstData = file_get_contents(realpath($firstPathToFile));
    $secondData = file_get_contents(realpath($secondPathToFile));
    $firstFormat = pathinfo($firstPathToFile, PATHINFO_EXTENSION);
    $secondFormat = pathinfo($secondPathToFile, PATHINFO_EXTENSION);
    $firstParseData = parse($firstData, $firstFormat);
    $secondParseData = parse($secondData, $secondFormat);
    $formatter = getFormatter($format);
    $diffResult = diff($firstParseData, $secondParseData);
    $result = $formatter($diffResult);
    return $result;
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
