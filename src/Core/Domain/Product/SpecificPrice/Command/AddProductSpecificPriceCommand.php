<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\Command;

use DateTime;
use DateTimeInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\CurrencyIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\NoCurrencyId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupId;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\GroupIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Customer\Group\ValueObject\NoGroupId;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationIdInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\NoCombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Exception\ProductConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject\Price;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\NoShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopIdInterface;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Reduction;
use PrestaShop\PrestaShop\Core\Util\DateTime\NullDateTime;

/**
 * Add specific price to a Product
 */
class AddProductSpecificPriceCommand
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var Reduction
     */
    private $reduction;

    /**
     * @var bool
     */
    private $includesTax;

    /**
     * @var Price
     */
    private $price;

    /**
     * @var int
     */
    private $fromQuantity;

    /**
     * @var ShopIdInterface
     */
    private $shopId;

    /**
     * @var CombinationIdInterface
     */
    private $combinationId;

    /**
     * @var CurrencyIdInterface
     */
    private $currencyId;

    /**
     * @var int
     *
     * @todo: countryId & customerId should also use the same convention of {Foo}IdInterface,
     *        but it requires some refactoring as it was already used in many places this primitive way
     */
    private $countryId = 0;

    /**
     * @var GroupIdInterface
     */
    private $groupId;

    /**
     * @var int
     */
    private $customerId = 0;

    /**
     * @var DateTimeInterface
     *
     * @see DateTime
     * @see NullDateTime
     */
    private $dateTimeFrom;

    /**
     * @var DateTimeInterface
     *
     * @see DateTime
     * @see NullDateTime
     */
    private $dateTimeTo;

    /**
     * @param int $productId
     * @param string $reductionType
     * @param string $reductionValue
     * @param bool $includeTax
     * @param string $price
     * @param int $fromQuantity
     *
     * @throws DomainConstraintException
     * @throws ProductConstraintException
     */
    public function __construct(
        int $productId,
        string $reductionType,
        string $reductionValue,
        bool $includeTax,
        string $price,
        int $fromQuantity
    ) {
        $this->productId = new ProductId($productId);
        $this->reduction = new Reduction($reductionType, $reductionValue);
        $this->includesTax = $includeTax;
        $this->price = new Price($price);
        $this->fromQuantity = $fromQuantity;
        $this->shopId = new NoShopId();
        $this->combinationId = new NoCombinationId();
        $this->currencyId = new NoCurrencyId();
        $this->groupId = new NoGroupId();
        $this->dateTimeFrom = new NullDateTime();
        $this->dateTimeTo = new NullDateTime();
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return Reduction
     */
    public function getReduction(): Reduction
    {
        return $this->reduction;
    }

    /**
     * @return bool
     */
    public function includesTax(): bool
    {
        return $this->includesTax;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @return int
     */
    public function getFromQuantity(): int
    {
        return $this->fromQuantity;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTimeFrom(): DateTimeInterface
    {
        return $this->dateTimeFrom;
    }

    /**
     * @param DateTimeInterface $dateTimeFrom
     *
     * @see DateTime
     * @see NullDateTime
     *
     * @return AddProductSpecificPriceCommand
     */
    public function setDateTimeFrom(DateTimeInterface $dateTimeFrom): self
    {
        $this->dateTimeFrom = $dateTimeFrom;

        return $this;
    }

    /**
     * @return DateTimeInterface
     */
    public function getDateTimeTo(): ?DateTimeInterface
    {
        return $this->dateTimeTo;
    }

    /**
     * @param DateTimeInterface $dateTimeTo
     *
     * @see DateTime
     * @see NullDateTime
     *
     * @return AddProductSpecificPriceCommand
     */
    public function setDateTimeTo(DateTimeInterface $dateTimeTo): self
    {
        $this->dateTimeTo = $dateTimeTo;

        return $this;
    }

    /**
     * @return ShopIdInterface
     */
    public function getShopId(): ShopIdInterface
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     *
     * @return $this
     */
    public function setShopId(int $shopId): self
    {
        if (NoShopId::NO_SHOP_ID === $shopId) {
            $this->shopId = new NoShopId();
        } else {
            $this->shopId = new ShopId($shopId);
        }

        return $this;
    }

    /**
     * @return CombinationIdInterface
     */
    public function getCombinationId(): CombinationIdInterface
    {
        return $this->combinationId;
    }

    /**
     * @param int $combinationId
     *
     * @return $this
     */
    public function setCombinationId(int $combinationId): self
    {
        if (NoCombinationId::NO_COMBINATION_ID === $combinationId) {
            $this->combinationId = new NoCombinationId();
        } else {
            $this->combinationId = new CombinationId($combinationId);
        }

        return $this;
    }

    /**
     * @return CurrencyIdInterface
     */
    public function getCurrencyId(): CurrencyIdInterface
    {
        return $this->currencyId;
    }

    /**
     * @param int $currencyId
     *
     * @return $this
     */
    public function setCurrencyId(int $currencyId): self
    {
        if (NoCurrencyId::NO_CURRENCY_ID === $currencyId) {
            $this->currencyId = new NoCurrencyId();
        } else {
            $this->currencyId = new CurrencyId($currencyId);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @param int $countryId
     *
     * @return $this
     */
    public function setCountryId(int $countryId): self
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * @return GroupIdInterface
     */
    public function getGroupId(): GroupIdInterface
    {
        return $this->groupId;
    }

    /**
     * @param int $groupId
     *
     * @return $this
     */
    public function setGroupId(int $groupId): self
    {
        if (NoGroupId::NO_GROUP_ID === $groupId) {
            $this->groupId = new NoGroupId();
        } else {
            $this->groupId = new GroupId($groupId);
        }

        return $this;
    }

    /**
     * @return int
     */
    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    /**
     * @param int $customerId
     *
     * @return $this
     */
    public function setCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;

        return $this;
    }
}
