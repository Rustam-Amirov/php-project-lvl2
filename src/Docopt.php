<?php
namespace Differ\Docopt;
/**
 * Short description
 *
 * Php version 7.4
 *
 * @category Components
 * @package  Learning_Project
 * @author   Rustam-Amirov <r.amirov@yahoo.com>
 * @license  https://mit-license.org MIT
 * @link     https://github.com/Rustam-Amirov/php-project-lvl2
 */

require_once __DIR__.'/../vendor/docopt/docopt/src/docopt.php';
use function Differ\GenDiff\genDiff;

function docopt() {
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
