<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Converter;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Webmozart\Assert\Assert;
use Sylius\Component\Core\Model\AdjustmentInterface;


final class OrderItemUnitsToLineItemsConverter implements LineItemsConverterInterface
{
    private TaxRatePercentageProviderInterface $taxRatePercentageProvider;

    private LineItemFactoryInterface $lineItemFactory;

    public function __construct(
        TaxRatePercentageProviderInterface $taxRatePercentageProvider,
        LineItemFactoryInterface $lineItemFactory
    ) {
        $this->taxRatePercentageProvider = $taxRatePercentageProvider;
        $this->lineItemFactory = $lineItemFactory;
    }

    public function convert(OrderInterface $order): array
    {
        $lineItems = [];

        /** @var OrderItemUnitInterface $unit */
        foreach ($order->getItemUnits() as $unit) {
            $lineItems = $this->addLineItem($this->convertOrderItemUnitToLineItem($unit), $lineItems);
        }

        return $lineItems;
    }

    private function convertOrderItemUnitToLineItem(OrderItemUnitInterface $unit): LineItemInterface
    {
        /** @var OrderItemInterface $item */
        $item = $unit->getOrderItem();

        $grossValue = $unit->getTotal();
        $taxAmount = $unit->getTaxTotal();
        $unitPrice = $grossValue - $taxAmount;
        $netValue = $grossValue - $taxAmount;
        // Discount
        $discount = $item->getAdjustmentsTotal('order_item_discount');
        $unitDiscount = $discount / $item->getQuantity();
        //
        $subTotal = $unitPrice + $unitDiscount;
        $total = $subTotal + $taxAmount;

        /** @var string|null $productName */
        $productName = $item->getProductName();
        Assert::notNull($productName);

        $variant = $item->getVariant();

        // Tax rate code
        $taxRateCode = null;
        $taxAdjustments = $unit->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        /** @var AdjustmentInterface|null $taxAdjustment */
        $taxAdjustment = $taxAdjustments->first();

        if($taxAdjustment) {
            $details = $taxAdjustment->getDetails();
            $taxRateCode = $details['taxRateCode'];
        }

        return $this->lineItemFactory->createWithData(
            $productName,
            1,
            $netValue,
            $subTotal,
            $taxAmount,
            $total,
            $unitDiscount,
            $item->getVariantName(),
            $variant !== null ? $variant->getCode() : null,
            $this->taxRatePercentageProvider->provideFromAdjustable($unit),
            $taxRateCode,
            $item->getVariant()
        );
    }

    /**
     * @param LineItemInterface[] $lineItems
     *
     * @return LineItemInterface[]
     */
    private function addLineItem(LineItemInterface $newLineItem, array $lineItems): array
    {
        foreach ($lineItems as $lineItem) {
            if ($lineItem->compare($newLineItem)) {
                $lineItem->merge($newLineItem);

                return $lineItems;
            }
        }

        $lineItems[] = $newLineItem;

        return $lineItems;
    }
}
