<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Funct\Collection\flatten;
use function Funct\Collection\flattenAll;

function genDiff($request, $secondPathToFile = false)
{
    if ($secondPathToFile) {
        [$firstData, $secondData] = getDataFiles($request, $secondPathToFile);
    } else {
        [$firstData, $secondData] = getDataFiles($request['<firstFile>'], $request['<secondFile>']);
    }
    $result = parsing($firstData, $secondData);
    return $result;
}

function stringify($key, $value, $prefix = ' ')
{
    if (is_bool($key) && is_bool($value)) {
        return "    " . $prefix . " " . json_encode($key) . ": " . json_encode($value) . "\n";
    } elseif (is_bool($key)) {
        return "    " . $prefix . " " . json_encode($key) . ": " . $value . "\n";
    } elseif (is_bool($value)) {
        return "    " . $prefix . " " . $key . ": " . json_encode($value) . "\n";
    } else {
        return "    " . $prefix . " " . $key . ": " . $value . "\n";
    }
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

function parsing($firstData, $secondData, $depth = '')
{
    $keys = array_unique(flatten([array_keys((array)$firstData), array_keys((array)$secondData)]));
    $result = array_reduce($keys, function ($acc, $key) use ($firstData, $secondData, $depth) {
        if (isset($firstData->$key) && isset($secondData->$key)) {
            if (is_object($firstData->$key) && is_object($secondData->$key)) {
                $acc = $acc . stringify($key, parsing($firstData->$key, $secondData->$key, $depth . '    '), $depth);
            } elseif ($secondData->$key === $firstData->$key) {
                $acc = $acc . stringify($key, $secondData->$key, $depth . ' ');
            } else {
                if (!is_object($firstData->$key) && !is_object($secondData->$key)) {
                    $acc = $acc . stringify($key, $secondData->$key, $depth . '+');
                    $acc = $acc . stringify($key, $firstData->$key, $depth . '-');
                }
            }
        } elseif (isset($secondData->$key)) {
            if (!is_object($secondData->$key) && !is_array($secondData->$key)) {
                $newDepth = (empty($firstData)) ? $depth . ' ' : $depth . '+';
                $acc = $acc . stringify($key, $secondData->$key, $newDepth);
            } else {
                $acc = $acc . stringify($key, parsing([], $secondData->$key, $depth . '    '), $depth . '+');
            }
        } elseif (isset($firstData->$key)) {
            if (!is_object($firstData->$key) && !is_array($firstData->$key)) {
                $newDepth = (empty($secondData)) ? $depth . ' ' : $depth . '-';
                $acc = $acc . stringify($key, $firstData->$key, $newDepth);
            } else {
                $acc = $acc . stringify($key, parsing($firstData->$key, [], $depth . '    '), $depth . '-');
            }
        }
            return $acc;
    }, "");
    return "{\n$result$depth}\n";
}
