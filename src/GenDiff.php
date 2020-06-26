<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Differ\GenDiff\Formatters\Pretty\pretty;
use function Differ\GenDiff\Formatters\Plain\plain;
use function Differ\GenDiff\Formatters\GetJson\getJson;

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
    $format = explode('.', $filePath)[sizeof(explode('.', $filePath)) - 1];
    $getData = parse($format);
    return $getData($filePath);
}

function parse($format)
{
    switch ($format) {
        case 'json':
            return function ($filePath) {
                $fileData = json_decode(file_get_contents(realpath($filePath)), false);
                return $fileData;
            };
        break;
        case 'yaml':
            return function ($filePath) {
                $fileData = Yaml::parse(file_get_contents(realpath($filePath)), Yaml::PARSE_OBJECT_FOR_MAP);
                return $fileData;
            };
        break;
        default:
            throw new \Exception("Неподдерживаемый формат файла: {$format}", 1);
    }
}


function getFormatter($format)
{
    switch ($format) {
        case 'pretty':
            return pretty();
        break;
        case 'plain':
            return plain();
        break;
        case 'json':
            return getJson();
        break;
    }
}


function diff($tree1, $tree2)
{
    $keys = array_keys(array_merge(get_object_vars($tree1), get_object_vars($tree2)));
    return  array_map(function ($k) use ($tree1, $tree2) {
        if (property_exists($tree2, $k) && property_exists($tree1, $k)) {
            if (is_object($tree1->$k) && is_object($tree2->$k)) {
                return collectNode($k, diff($tree1->$k, $tree2->$k), [], 'nested');
            } elseif ($tree1->$k == $tree2->$k) {
                return collectNode($k, [], $tree1->$k, 'unchanged');
            } else {
                return collectNode($k, [], ['old' => $tree1->$k, 'new' => $tree2->$k], 'changed');
            }
        } elseif (!property_exists($tree2, $k)) {
            if (is_object($tree1->$k)) {
                return collectNode($k, getchildren($tree1->$k), [], 'deleted');
            } else {
                return collectNode($k, [], $tree1->$k, 'deleted');
            }
        } elseif (!property_exists($tree1, $k)) {
            if (is_object($tree2->$k)) {
                return collectNode($k, getchildren($tree2->$k), [], 'add');
            } else {
                return collectNode($k, [], $tree2->$k, 'add');
            }
        }
    }, $keys);
}


function getChildren($tree)
{
    $keys  = array_keys((array)$tree);
    return array_map(function ($key) use ($tree) {
        if (!is_object($tree->$key)) {
            return collectNode($key, [], $tree->$key, 'nested');
        } else {
            return collectNode($key, getchildren($tree->$key), [], 'unchanged');
        }
    }, $keys);
}

function collectNode($key, $children, $value, $diff)
{
    return ['key' => $key, 'children' => $children, 'value' => $value, 'diff' => $diff];
}
