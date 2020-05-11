<?php
/*
MIT License

Copyright (c) 2020 Vaibhav Kubre

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
*/
function convertPhpToHtml($c)
{
    if (!file_exists($c['op']) || !is_dir($c['op'])) mkdir($c['op']);
    $log_file = fopen('conversion.log', 'a');

    $files =  new RecursiveIteratorIterator(new RecursiveDirectoryIterator(getcwd() . DIRECTORY_SEPARATOR . $c['ip']));
    ob_start();
    foreach ($files as $file) {
        if (
            $file->isDir()
            || (!$c['convert-includes'] && preg_match('/inc|include|includes/i', $file->getRealPath()))
            || (!$c['copy-git'] && preg_match('/\.git/i', $file->getRealPath()))
        ) continue;

        if ($file->getExtension() === "php") {
            ob_clean();
            include($file->getRealPath());
            $data = ob_get_contents();
            if ($c['replace-href'])
                $data = preg_replace('/(<\s*a[^>]*href=["a-z]*\.)php/i', '$1html', $data);
            $n = new SplFileObject($c['op'] . DIRECTORY_SEPARATOR . str_replace('.php', '', $file->getFilename()) . '.html', 'w');
            if ($n->isWritable()) {
                $n->fwrite($data);
            } else {
                fwrite($log_file, "Unable to write: " . $file->getRealPath());
            }
        } else {
            $dir = str_replace("/{$c['ip']}/", "/{$c['op']}/", $file->getPath());
            if (!file_exists($dir)) mkdir($dir, 0755, true);
            copy($file->getRealPath(), str_replace("/{$c['ip']}/", "/{$c['op']}/", $file->getRealPath()));
        }
    }
    ob_end_clean();
}

$c = [
    "op" => "html",
    "convert-includes" => false,
    "copy-git" => false,
    "replace-href" => true
];
array_shift($argv);
foreach ($argv as $arg) {
    list($option, $val) = explode("=", $arg);
    $c[$option] = $val;
}

if (!isset($c["ip"]) || empty($c["ip"]))
    exit("Provide input folder!");

$c["convert-includes"] = (bool) $c["convert-includes"];
$c["copy-git"] = (bool) $c["copy-git"];
$c["replace-href"] = (bool) $c["replace-href"];
convertPhpToHtml($c);
echo "DONE! CHECK {$c['op']}", PHP_EOL;
