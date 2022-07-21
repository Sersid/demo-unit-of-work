<?php
declare(strict_types=1);

$repository = new \App\UnitOfWork\Example1\ProductRepository();


$product = $repository->getByCode(100008);
$product->rename('New name');


$product2 = $repository->getByCode(100008);
$name = $product2->getName(); // not "New name"


// $product !== $product2