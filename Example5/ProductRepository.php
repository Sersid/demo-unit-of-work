<?php
declare(strict_types=1);

namespace App\UnitOfWork\Example5;

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
                ->setSelect(['ID', 'NAME'])
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

    public function add(Product $product): void
    {
        $this->identityMap[$product->getCode()] = $product;
    }

    public function delete(Product $product): void
    {
        unset($this->identityMap[$product->getCode()]);
    }

    public function commit(): void
    {
        $data = $this->data;
        $this->data = [];

        foreach ($this->identityMap as $product) {
            $code = $product->getCode();
            $arProduct = [
                'IBLOCK_ID' => IB_GOODS_OPT,
                'XML_ID' => $code,
                'NAME' => $product->getName(),
            ];
            if (isset($data[$code])) {
                // update
                $arProduct['ID'] = $data[$code]['ID'];
                if ($data[$code]['NAME'] !== $arProduct['NAME']) {
                    ElementTable::update($arProduct['ID'], $arProduct);
                }
            } else {
                // add
                $obResult = ElementTable::add($arProduct);
                $arProduct['ID'] = (int)$obResult->getId();
            }
            $this->data[$code] = $arProduct;
            unset($data[$code]);
        }
        // delete
        foreach ($data as $arProduct) {
            ElementTable::delete($arProduct['ID']);
        }
    }
}