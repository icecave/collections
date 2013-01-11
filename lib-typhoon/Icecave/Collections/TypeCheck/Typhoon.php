<?php
namespace Icecave\Collections\TypeCheck;


abstract class Typhoon
{
    public static function get($className, array $arguments = null)
    {
        if (static::$dummyMode)
        {
            return (new DummyValidator());
        }
        if ((!\array_key_exists($className, static::$instances)))
        {
            static::install($className, static::createValidator($className));
        }
        ($validator = static::$instances[$className]);
        if ((null !== $arguments))
        {
            $validator->validateConstruct($arguments);
        }
        return $validator;
    }
    public static function install($className, $validator)
    {
        (static::$instances[$className] = $validator);
    }
    public static function setRuntimeGeneration($runtimeGeneration)
    {
        (static::$runtimeGeneration = $runtimeGeneration);
    }
    public static function runtimeGeneration()
    {
        return static::$runtimeGeneration;
    }
    protected static function createValidator($className)
    {
        ($validatorClassName = ('Icecave\\Collections\\TypeCheck\\Validator\\' . $className . 'Typhoon'));
        if ((static::runtimeGeneration() && (!\class_exists($validatorClassName))))
        {
            (static::$dummyMode = true);
            static::defineValidator($className);
            (static::$dummyMode = false);
        }
        return (new $validatorClassName);
    }
    protected static function defineValidator($className, \Eloquent\Typhoon\Generator\ValidatorClassGenerator $classGenerator = null)
    {
        if ((null === $classGenerator))
        {
            ($classGenerator = (new \Eloquent\Typhoon\Generator\ValidatorClassGenerator));
        }
        eval(('?>' . $classGenerator->generateFromClass(static::configuration(), (new \ReflectionClass($className)))));
    }
    protected static function configuration()
    {
        return (new \Eloquent\Typhoon\Configuration\RuntimeConfiguration('Icecave\\Collections\\TypeCheck', false));
    }
    private static $instances = array();
    private static $dummyMode = false;
    private static $runtimeGeneration = false;
}
