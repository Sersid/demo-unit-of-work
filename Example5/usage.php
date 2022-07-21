<?php
declare(strict_types=1);

$repository = new \App\UnitOfWork\Example5\ProductRepository();


$product = $repository->getByCode(100008);
$product->rename('New name');


$product2 = $repository->getByCode(100500);


$newProduct = new \App\UnitOfWork\Product(123456, 'Name');
$repository->add($newProduct);


$product3 = $repository->getByCode(999999);
$repository->delete($product3);


$repository->commit();
$repository->commit(); // без изменений в бд