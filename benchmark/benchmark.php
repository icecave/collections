<?php
require __DIR__ . '/../vendor/autoload.php';

class Benchmark
{
    public static function run(
        $iterations,
        $setup,
        $work
    ) {
        $trace = debug_backtrace();

        echo str_replace(
            array(__DIR__ . '/', '/', '.php'),
            array('', '::', ''),
            $trace[0]['file']
        ) . ' x ' . $iterations . ': ';

        if ($setup) {
            for ($i = 0; $i < $iterations; ++$i) {
                $setup($i);
            }
        }

        $begin = microtime(true);

        for ($i = 0; $i < $iterations; ++$i) {
            $work($i);
        }

        $end = microtime(true);

        echo ($end - $begin) . 's' . PHP_EOL;
    }
}
