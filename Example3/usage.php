<?php
declare(strict_types=1);

$repository = new \App\UnitOfWork\Example3\ProductRepository();


$product = $repository->getByCode(100008);


$product2 = $repository->getByCode(123456);
$product2->rename('New name');


$repository->commit();