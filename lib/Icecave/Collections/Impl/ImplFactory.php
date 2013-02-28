<?php
namespace Icecave\Collections\Impl;

class ImplFactory
{
    public function __construct($implementation)
    {
        $this->implementation = $implementation;
    }

    public static function instance()
    {
        if (null !== static::$instance) {
            return static::$instance;
        } elseif (extension_loaded('icecave_collections')) {
            return static::$instance = new static('Extension');
        } else {
            return static::$instance = new static('Native');
        }
    }

    public function className($name)
    {
        return sprintf('%s\\%s\\%sImpl', __NAMESPACE__, $this->implementation, $name);
    }

    public function create($name)
    {
        $class = $this->className($name);

        return new $class;
    }

    private static $instance;
    private $implementation;
}
