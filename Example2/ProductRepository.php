<?php
declare(strict_types=1);

namespace App\UnitOfWork\Example2;

use App\UnitOfWork\Product;
use Bitrix\Iblock\ElementTable;
use RuntimeException;

class ProductRepository
{
    /** @var array<int, Product>  */
    private array $identityMap = [];

    public function getByCode(int $code): Product
    {
        if (!isset($this->identityMap[$code])) {
            $arProduct = ElementTable::query()
                ->setSelect(['NAME'])
                ->setFilter(['IBLOCK_ID' => IB_GOODS_OPT, 'XML_ID' => $code])
                ->exec()
                ->fetch();

            if (empty($arProduct)) {
                throw new RuntimeException('����� � ����� "' . $code . '" �� ������');
            }

            $this->identityMap[$code] = new Product($code, $arProduct['NAME']);
        }

        return $this->identityMap[$code];
    }
}