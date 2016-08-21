<?php

use Symfony\Component\ClassLoader\ClassLoader;

require_once(__DIR__.'/../../autoload.php');

function deleteTree($path)
{
    if (!is_dir($path)) {
        return;
    }

    $iterator = new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS);
    foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $subpath) {
        if ($subpath->isDir()) {
            rmdir((string) $subpath);
        } else {
            unlink((string) $subpath);
        }
    }

    rmdir($path);
}

// Wipe previously generated proxies
deleteTree(__DIR__.'/../../test/proxies/Doctrine/OrientDB/Proxy/test/Doctrine');
deleteTree(__DIR__.'/../../test/proxies/Doctrine/OrientDB/Proxy/test/Integration');

// Set up autoloading for testing
$loader = new ClassLoader();

$loader->addPrefixes(array(
    'Doctrine\OrientDB\Proxy' => __DIR__.'/../../test/proxies/',
    'test' => __DIR__.'/../../',
));

$loader->register();

