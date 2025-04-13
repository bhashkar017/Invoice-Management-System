<?php
echo "Checking required PHP extensions:\n\n";

$required_extensions = array(
    'gd' => 'GD Library',
    'imagick' => 'ImageMagick',
    'mysqli' => 'MySQLi',
    'mbstring' => 'Multibyte String',
    'xml' => 'XML'
);

foreach ($required_extensions as $extension => $name) {
    if (extension_loaded($extension)) {
        echo "✓ $name ($extension) is installed\n";
    } else {
        echo "✗ $name ($extension) is NOT installed\n";
    }
}

echo "\nPHP Version: " . PHP_VERSION;
?> 