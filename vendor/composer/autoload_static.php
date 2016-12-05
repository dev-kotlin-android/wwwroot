<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit0e24f167bb78b26223ea92133ebf94e6
{
    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\composer\\' => 15,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\composer\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-installer/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit0e24f167bb78b26223ea92133ebf94e6::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit0e24f167bb78b26223ea92133ebf94e6::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}