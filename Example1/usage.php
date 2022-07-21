<?php
declare(strict_types=1);

$repository = new \App\UnitOfWork\Example1\ProductRepository();


$product = $repository->getByCode(100008);