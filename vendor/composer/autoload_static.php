<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6624e64b43d8b10f84ff63c345cfa51d
{
    public static $files = array (
        '5d80ba682afba25d348d62676196765b' => __DIR__ . '/../..' . '/src/functions.php',
    );

    public static $classMap = array (
        'HTML_Forms\\Actions\\Action' => __DIR__ . '/../..' . '/src/Actions/Action.php',
        'HTML_Forms\\Actions\\Email' => __DIR__ . '/../..' . '/src/Actions/Email.php',
        'HTML_Forms\\Admin\\Admin' => __DIR__ . '/../..' . '/src/Admin/Admin.php',
        'HTML_Forms\\Admin\\Table' => __DIR__ . '/../..' . '/src/Admin/Table.php',
        'HTML_Forms\\Form' => __DIR__ . '/../..' . '/src/Form.php',
        'HTML_Forms\\Forms' => __DIR__ . '/../..' . '/src/Forms.php',
        'HTML_Forms\\Submission' => __DIR__ . '/../..' . '/src/Admin/Submission.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInit6624e64b43d8b10f84ff63c345cfa51d::$classMap;

        }, null, ClassLoader::class);
    }
}