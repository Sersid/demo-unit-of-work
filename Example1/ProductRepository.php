<?php
declare(strict_types=1);

namespace App\UnitOfWork\Example1;

use App\UnitOfWork\Product;
use Bitrix\Iblock\ElementTable;
use RuntimeException;

class ProductRepository
{
    public function getByCode(int $code): Product
    {
        $arProduct = ElementTable::query()
            ->setSelect(['NAME'])
            ->setFilter(['IBLOCK_ID' => IB_GOODS_OPT, 'XML_ID' => $code])
            ->exec()
            ->fetch();

        if (empty($arProduct)) {
            throw new RuntimeException('Товар с кодом "' . $code . '" не найден');
        }

        return new Product($code, $arProduct['NAME']);
    }
}