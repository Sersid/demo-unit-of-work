<?php
declare(strict_types=1);

namespace App\UnitOfWork\Example4;

use App\UnitOfWork\Product;
use Bitrix\Iblock\ElementTable;
use RuntimeException;

class ProductRepository
{
    /** @var array<int, Product>  */
    private array $identityMap = [];
    /** @var array<int, array>  */
    private array $data = [];

    public function getByCode(int $code): Product
    {
        if (!isset($this->identityMap[$code])) {
            $arProduct = ElementTable::query()
                ->setSelect(['NAME'])
                ->setFilter(['IBLOCK_ID' => IB_GOODS_OPT, 'XML_ID' => $code])
                ->exec()
                ->fetch();

            if (empty($arProduct)) {
                throw new RuntimeException('Товар с кодом "' . $code . '" не найден');
            }

            $this->data[$code] = $arProduct;
            $this->identityMap[$code] = new Product($code, $arProduct['NAME']);
        }

        return $this->identityMap[$code];
    }

    public function commit(): void
    {
        foreach ($this->identityMap as $product) {
            $code = $product->getCode();
            if ($this->data[$code]['NAME'] !== $product->getName()) {
                $this->data[$code]['NAME'] = $product->getName();
                ElementTable::update($this->data[$code]['ID'], $this->data[$code]);
            }
        }
    }
}