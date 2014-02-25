<?php
require __DIR__ . '/../benchmark.php';

$vector = new Icecave\Collections\Vector;

Benchmark::run(
    50000,
    null,
    function ($i) use ($vector) {
        $vector->pushFront($i);
    }
);
