<?php

function printTestCase1(...$param)
{
    for ($i = 1; $i <= 100000; $i++) {
        echo "Test From printTestCase1 Item $i\n";
    }
}

echo printTestCase1();