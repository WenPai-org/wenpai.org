<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit06367dcc62b511a0b31736c78a3494f1
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit06367dcc62b511a0b31736c78a3494f1::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit06367dcc62b511a0b31736c78a3494f1::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit06367dcc62b511a0b31736c78a3494f1::$classMap;

        }, null, ClassLoader::class);
    }
}