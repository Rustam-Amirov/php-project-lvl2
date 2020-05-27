<?php

namespace Differ\Docopt;

require_once __DIR__ . '/../vendor/docopt/docopt/src/docopt.php';
use function Differ\GenDiff\genDiff;

function docopt()
{
    $doc = <<<DOC

Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format <fmt>] <firstFile> <secondFile>

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format <fmt>                Report format [default: pretty]
DOC;

    genDiff(\Docopt::handle($doc, ['version' => 'v0.01']));
}
