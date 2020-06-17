<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Funct\Collection\flatten;
use function Differ\GenDiff\Formatters\Pretty\pretty;
use function Differ\GenDiff\Formatters\Plain\plain;

function genDiff($firstPathToFile, $secondPathToFile, $format = 'pretty')
{
    [$firstData, $secondData] = getDataFiles($firstPathToFile, $secondPathToFile);
    $formatter = getForamtter($format);
    $diffResult = diff($firstData, $secondData);
    $result = $formatter($diffResult);
    return $result;
}

function getDataFiles($firstPathToFile, $secondPathToFile)
{
    $getData = getParser($firstPathToFile);
    if (file_exists($secondPathToFile) && file_exists(($firstPathToFile))) {
        return $getData($firstPathToFile, $secondPathToFile);
    } elseif (file_exists(__DIR__ . '/' . $firstPathToFile) && file_exists(__DIR__ . '/' . $secondPathToFile)) {
        return $getData(__DIR__ . '/' . $firstPathToFile, __DIR__ . '/' . $secondPathToFile);
    } else {
        return false;
    }
}

function getParser($firstFile)
{
    $format = explode('.', $firstFile)[sizeof(explode('.', $firstFile)) - 1];
    switch ($format) {
        case 'json':
            return function ($firstFile, $secondFile) {
                $firstData = json_decode(file_get_contents($firstFile), false);
                $secondData = json_decode(file_get_contents($secondFile), false);
                return [$firstData, $secondData];
            };
        break;
        case 'yaml':
            return function ($firstFile, $secondFile) {
                $firstData = Yaml::parse(file_get_contents($firstFile), Yaml::PARSE_OBJECT_FOR_MAP);
                $secondData = Yaml::parse(file_get_contents($secondFile), Yaml::PARSE_OBJECT_FOR_MAP);
                return [$firstData, $secondData];
            };
        break;
    }
}


function getForamtter($format)
{
    switch ($format) {
        case 'pretty':
            return pretty();
        break;
        case 'plain':
            return plain();
        break;
    }
}


function diff($tree1, $tree2, $path = '')
{
    $keys = array_unique(flatten([array_keys((array)$tree1), array_keys((array)$tree2)]));
    return  array_map(function ($k) use ($tree1, $tree2, $path) {
        if (isset($tree2->$k) && isset($tree1->$k)) {
            if (is_object($tree1->$k) && is_object($tree2->$k)) {
                $children = diff($tree1->$k, $tree2->$k, "$path/$k");
                return ['key' => $k, 'children' => $children, 'path' => "$path/$k", 'diff' => ' '];
            } elseif ($tree1->$k == $tree2->$k) {
                return ['key' => $k, 'value' => $tree1->$k, 'path' => "$path/$k", 'diff' => '='];
            } else {
                $value1 = $tree1->$k;
                $value2 = $tree2->$k;
                return ['key' => $k, 'value1' => $value1,'value2' => $value2, 'path' => "$path/$k", 'diff' => '!'];
            }
        } elseif (!isset($tree2->$k)) {
            if (is_object($tree1->$k)) {
                $children = getValue($tree1->$k, $path . '/' . $k);
                return ['key' => $k, 'children' => $children, 'path' => "$path/$k", 'diff' => '-'];
            } else {
                return ['key' => $k, 'value' => $tree1->$k, 'path' => "$path/$k", 'diff' => '-'];
            }
        } elseif (!isset($tree1->$k)) {
            if (is_object($tree2->$k)) {
                $children = getValue($tree2->$k, $path . '/' . $k);
                return ['key' => $k, 'children' => $children, 'path' => "$path/$k", 'diff' => '+'];
            } else {
                return ['key' => $k, 'value' => $tree2->$k, 'path' => "$path/$k", 'diff' => '+'];
            }
        }
    }, $keys);
}


function getValue($tree, $path = '/')
{
    $keys  = array_keys((array)$tree);
    return array_map(function ($key) use ($tree, $path) {
        if (!is_object($tree->$key)) {
            return ['key' => $key, 'value' => $tree->$key, 'path' => $path . '/' . $key, 'diff' => ' '];
        } else {
            $children = getValue($tree->$key, $path . '/' . $key);
            return ['key' => $key, 'children' => $children, 'path' => $path . '/' . $key];
        }
    }, $keys);
}
