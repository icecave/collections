<?php
require __DIR__ . '/../benchmark.php';

$vector = new Icecave\Collections\Vector;

Benchmark::run(
    50000,
    function ($i) use ($vector) {
        $vector->pushBack($i);
    },
    function ($i) use ($vector) {
        $vector->popBack();
    }
);
