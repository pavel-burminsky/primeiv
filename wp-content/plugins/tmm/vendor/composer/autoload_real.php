<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit446c82857db1c7f2af1d129168a5924f
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit446c82857db1c7f2af1d129168a5924f', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit446c82857db1c7f2af1d129168a5924f', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit446c82857db1c7f2af1d129168a5924f::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
