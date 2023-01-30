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

use Sylius\Component\Core\Model\AdjustmentInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Exception\MoreThanOneTaxAdjustment;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Webmozart\Assert\Assert;


final class ShippingAdjustmentsToLineItemsConverter implements LineItemsConverterInterface
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

        /** @var AdjustmentInterface $shippingAdjustment */
        foreach ($order->getAdjustments(AdjustmentInterface::SHIPPING_ADJUSTMENT) as $shippingAdjustment) {
            $lineItems[] = $this->convertShippingAdjustmentToLineItem($shippingAdjustment);
        }

        return $lineItems;
    }

    private function convertShippingAdjustmentToLineItem(AdjustmentInterface $shippingAdjustment): LineItemInterface
    {
        /** @var ShipmentInterface|null $shipment */
        $shipment = $shippingAdjustment->getShipment();
        Assert::notNull($shipment);
        Assert::isInstanceOf($shipment, AdjustableInterface::class);

        $grossValue = $shipment->getAdjustmentsTotal();
        $taxAdjustment = $this->getShipmentTaxAdjustment($shipment);
        $taxAmount = $taxAdjustment !== null ? $taxAdjustment->getAmount() : 0;
        $netValue = $grossValue - $taxAmount;
        // Discount
        $discount = $shipment->getAdjustmentsTotal(AdjustmentInterface::ORDER_SHIPPING_PROMOTION_ADJUSTMENT);
        //
        $subTotal = $netValue;
        $total = $subTotal + $taxAmount;

        // Tax rate code
        $taxRateCode = null;

        if($taxAdjustment) {
            $details = $taxAdjustment->getDetails();
            $taxRateCode = $details['taxRateCode'];
        }


        return $this->lineItemFactory->createWithData(
            $shippingAdjustment->getLabel(),
            1,
            $netValue,
            $subTotal,
            $taxAmount,
            $total,
            $discount,
            null,
            null,
            $this->taxRatePercentageProvider->provideFromAdjustable($shipment),
            $taxRateCode,
            null,
            $shipment,
            null
        );
    }

    private function getShipmentTaxAdjustment(ShipmentInterface $shipment): ?AdjustmentInterface
    {
        $taxAdjustments = $shipment->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT);
        if ($taxAdjustments->isEmpty()) {
            return null;
        }

        if ($taxAdjustments->count() > 1) {
            throw MoreThanOneTaxAdjustment::occur();
        }

        /** @var AdjustmentInterface $taxAdjustment */
        $taxAdjustment = $taxAdjustments->first();

        return $taxAdjustment;
    }
}
