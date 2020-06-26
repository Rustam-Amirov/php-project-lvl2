<img src = "https://github.com/Rustam-Amirov/php-project-lvl2/workflows/CI/badge.svg?branch=master"></img>
[![Maintainability](https://api.codeclimate.com/v1/badges/bb2d60df0a85e2974405/maintainability)](https://codeclimate.com/github/Rustam-Amirov/php-project-lvl2/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/bb2d60df0a85e2974405/test_coverage)](https://codeclimate.com/github/Rustam-Amirov/php-project-lvl2/test_coverage)
<h1>Gendiff</h1>
<h2>Это учебный проект в котором реализована утилита для сравнения файлов.</h2>
<h3>Для установки через composer введите в консоли:</h3>
<pre>composer global require rustam/php-project-lvl2</pre>
<h3>Наберите  gendiff -h для помощи.</h3>
<pre>
Generate diff

Usage:
  gendiff (-h|--help)
  gendiff (-v|--version)
  gendiff [--format &lt;fmt&gt;] &lt;firstFile&gt; &lt;secondFile&gt;

Options:
  -h --help                     Show this screen
  -v --version                  Show version
  --format &lt;fmt&gt;                Report format [default: pretty]
</pre>

<h3> Также эту утилиту можно использовать как функцию. Пример:</h3>
<pre>genDiff($before.json, $after.json, $format);</pre>
<p>Где $before.json after.json пути до файлов. $format - формат вывода.
<p>Для сравнения файлов вы можете использовать абсолютные и относительные пути до файлов</p>

<a href="https://asciinema.org/a/334526?autoplay=1"><img src="https://asciinema.org/a/334526.png" width="700"/></a>
<p>Утилита также способна сравнивать файлы со вложенной структурой.</p>
<a href="https://asciinema.org/a/Q9BBEpxupk8ahrhMqUe33GcHY"><img src="https://asciinema.org/a/Q9BBEpxupk8ahrhMqUe33GcHY.png" width= "700"/></a>
<p>Утилита способна выводить результат в трех вариантах</p>
<div>-Plian</div>
<div>-Pretty  [default]</div>
<div>-JSON</div>
</br>
<p>JSON:</p>
<a href="https://asciinema.org/a/1StSb2hC6UhjmPfWBMkEQw7F5" target="_blank"><img src="https://asciinema.org/a/1StSb2hC6UhjmPfWBMkEQw7F5.svg" width = "700"/></a>
<p>Plain:</p>
<a href="https://asciinema.org/a/uXp5S8k9OVwTrz9B4RQHPiaUz" target="_blank"><img src="https://asciinema.org/a/uXp5S8k9OVwTrz9B4RQHPiaUz.svg" width = "700"/></a>
<p>Поддерживаемые форматы файлов для сравнения: .json .yaml</p>