<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Funct\Collection\flatten;
use function Differ\GenDiff\Formatters\Pretty\pretty;
use function Differ\GenDiff\Formatters\Plain\plain;
use function Differ\GenDiff\Formatters\GetJson\getJson;

function genDiff($firstPathToFile, $secondPathToFile, $format = 'pretty')
{
    $firstData = getDataFiles($firstPathToFile);
    $secondData = getDataFiles($secondPathToFile);
    $formatter = getForamtter($format);
    $diffResult = diff($firstData, $secondData);
    $result = $formatter($diffResult);
    return $result;
}

function getDataFiles($path)
{
    $format = explode('.', $path)[sizeof(explode('.', $path)) - 1];
    $getData = getParser($path, $format);
    if (file_exists($path)) {
        return $getData($path);
    } elseif (file_exists(__DIR__ . '/' . $path)) {
        return $getData(__DIR__ . '/' . $path);
    } else {
        return false;
    }
}

function getParser($file, $format)
{
    switch ($format) {
        case 'json':
            return function ($file) {
                $fileData = json_decode(file_get_contents($file), false);
                return $fileData;
            };
        break;
        case 'yaml':
            return function ($file) {
                $fileData = Yaml::parse(file_get_contents($file), Yaml::PARSE_OBJECT_FOR_MAP);
                return $fileData;
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
        case 'json':
            return getJson();
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
