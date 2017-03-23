<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitb018e811df52ec1605f0690542ba5264
{
    public static $files = array (
        '8c331ccee4a7ae472d7efbc1ec99484e' => __DIR__ . '/../..' . '/delayed.php',
    );

    public static $prefixLengthsPsr4 = array (
        'D' => 
        array (
            'Delayed\\' => 8,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Delayed\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitb018e811df52ec1605f0690542ba5264::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitb018e811df52ec1605f0690542ba5264::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}