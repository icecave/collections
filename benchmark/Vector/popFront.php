<?php
require __DIR__ . '/../benchmark.php';

$vector = new Icecave\Collections\Vector;

Benchmark::run(
    1000,
    function ($i) use ($vector) {
        $vector->pushFront($i);
    },
    function ($i) use ($vector) {
        $vector->popFront();
    }
);
