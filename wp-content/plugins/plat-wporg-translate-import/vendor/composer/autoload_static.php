<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit22ffe314bc857e5d7de4575e67a45733
{
    public static $files = array (
        '41e4a9c7e0bd4f0ed8eb8c2024d20809' => __DIR__ . '/../..' . '/helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Platform\\Translate\\WPOrgTranslateImport\\' => 40,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Platform\\Translate\\WPOrgTranslateImport\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Platform\\Translate\\WPOrgTranslateImport\\Command\\Release' => __DIR__ . '/../..' . '/Command/Release.php',
        'Platform\\Translate\\WPOrgTranslateImport\\Command\\Worker' => __DIR__ . '/../..' . '/Command/Worker.php',
        'Platform\\Translate\\WPOrgTranslateImport\\Plugin' => __DIR__ . '/../..' . '/Plugin.php',
        'Platform\\Translate\\WPOrgTranslateImport\\Service\\Project' => __DIR__ . '/../..' . '/Service/Project.php',
        'Platform\\Translate\\WPOrgTranslateImport\\Web\\Import' => __DIR__ . '/../..' . '/Web/Import.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit22ffe314bc857e5d7de4575e67a45733::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit22ffe314bc857e5d7de4575e67a45733::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit22ffe314bc857e5d7de4575e67a45733::$classMap;

        }, null, ClassLoader::class);
    }
}
