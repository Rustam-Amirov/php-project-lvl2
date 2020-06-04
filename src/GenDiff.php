<?php

namespace Differ\GenDiff;

use Symfony\Component\Yaml\Yaml;

use function Funct\Collection\flattenAll;

function genDiff($request, $secondPathToFile = false)
{
    if ($secondPathToFile) {
        [$firstData, $secondData] = getDataFiles($request, $secondPathToFile);
    } else {
        [$firstData, $secondData] = getDataFiles($request['<firstFile>'], $request['<secondFile>']);
    }
    $keys = [array_keys((array)$firstData), array_keys((array)$secondData)];
    $result = array_reduce(array_unique(flattenAll($keys)), function ($acc, $key) use ($firstData, $secondData) {
        if (isset($firstData->$key) && isset($secondData->$key)) {
            if ($secondData->$key === $firstData->$key) {
                $acc = $acc . stringify($key, $secondData->$key);
            } else {
                $acc = $acc . stringify($key, $secondData->$key, '-');
                $acc = $acc . stringify($key, $firstData->$key, '+');
            }
        } elseif (isset($secondData->$key)) {
            $acc = $acc . stringify($key, $secondData->$key, '-');
        } else {
            $acc = $acc . stringify($key, $firstData->$key, '+');
        }
        return $acc;
    }, "");
    return "{\n$result}\n";
}


function stringify($key, $value, $prefix = ' ')
{
    if (is_bool($key) && is_bool($value)) {
        return $prefix . " " . json_encode($key) . ": " . json_encode($value) . "\n";
    } elseif (is_bool($key)) {
        return $prefix . " " . json_encode($key) . ": " . $value . "\n";
    } elseif (is_bool($value)) {
        return $prefix . " " . $key . ": " . json_encode($value) . "\n";
    } else {
        return $prefix . " " . $key . ": " . $value . "\n";
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
