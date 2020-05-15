<?php
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

$doc = <<<DOC
Usage: my_program.php [-hso FILE] [--quiet | --verbose] [INPUT ...]

Options:
  -h --help    show this
  -s --sorted  sorted output
  -o FILE      specify output file [default: ./test.txt]
  --quiet      print less text
  --verbose    print more text
DOC;
$args = Docopt::handle($doc);

// short form (5.4 or better)

$args = (new \Docopt\Handler)->handle($doc);
// long form, simple API (equivalent to short)
$params = array(
    'argv'=>array_slice($_SERVER['argv'], 1),
    'help'=>true,
    'version'=>null,
    'optionsFirst'=>false,
);
$args = Docopt::handle($doc, $params);
// long form, full API
$handler = new \Docopt\Handler(array('help'=>true, 'optionsFirst'=>false ));
$argv = (isset($argv)) ? $argv : '';
$handler->handle($doc, $argv);
