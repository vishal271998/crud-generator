<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit2d1ecfbc6afc43d230311657559e7ea7
{
    public static $prefixLengthsPsr4 = array (
        'V' => 
        array (
            'Vishal\\CrudGenerator\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Vishal\\CrudGenerator\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit2d1ecfbc6afc43d230311657559e7ea7::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit2d1ecfbc6afc43d230311657559e7ea7::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
