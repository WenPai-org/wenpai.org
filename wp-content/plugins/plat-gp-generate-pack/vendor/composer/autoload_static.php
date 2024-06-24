<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite83f3176e25048bda1b47b37d6b31d35
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Platform\\Translate\\GeneratePack\\' => 32,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Platform\\Translate\\GeneratePack\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Platform\\Translate\\GeneratePack\\Command\\Worker' => __DIR__ . '/../..' . '/Command/Worker.php',
        'Platform\\Translate\\GeneratePack\\Plugin' => __DIR__ . '/../..' . '/Plugin.php',
        'Platform\\Translate\\GeneratePack\\Service\\Pack' => __DIR__ . '/../..' . '/Service/Pack.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite83f3176e25048bda1b47b37d6b31d35::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite83f3176e25048bda1b47b37d6b31d35::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite83f3176e25048bda1b47b37d6b31d35::$classMap;

        }, null, ClassLoader::class);
    }
}
