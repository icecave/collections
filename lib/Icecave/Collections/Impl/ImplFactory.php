<?php
namespace Icecave\Collections\Impl;

class ImplFactory
{
    /**
     * @param string $implementation
     */
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

    /**
     * @param string $name
     */
    public function className($name)
    {
        return sprintf('%s\\%s\\%sImpl', __NAMESPACE__, $this->implementation, $name);
    }

    /**
     * @param string $name
     */
    public function create($name)
    {
        $class = $this->className($name);

        return new $class;
    }

    private static $instance;
    private $implementation;
}
