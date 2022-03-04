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
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Order\Model\AdjustableInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\InvoicingPlugin\Factory\LineItemFactoryInterface;
use Sylius\InvoicingPlugin\Provider\TaxRatePercentageProviderInterface;
use Webmozart\Assert\Assert;

/**
 * This converter requires SklinetSyliusPaymentFeePlugin plugin to be installed
 */
final class PaymentFeeAdjustmentsToLineItemsConverter implements LineItemsConverterInterface
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

        if (!interface_exists('\Sklinet\SyliusPaymentFeePlugin\Model\AdjustmentInterface')) {
            return $lineItems;
        }

        /** @var AdjustmentInterface $shippingAdjustment */
        foreach ($order->getAdjustments(
            \Sklinet\SyliusPaymentFeePlugin\Model\AdjustmentInterface::PAYMENT_ADJUSTMENT
        ) as $paymentFeeAdjustment) {
            $lineItems[] = $this->convertPaymentFeeAdjustmentToLineItem($paymentFeeAdjustment);
        }

        return $lineItems;
    }

    private function convertPaymentFeeAdjustmentToLineItem(AdjustmentInterface $paymentFeeAdjustment): LineItemInterface
    {
        if(!method_exists($paymentFeeAdjustment, 'getPayment')) {
            throw new \InvalidArgumentException('Plugin "%s" need to be installed', 'SklinetSyliusPaymentFeePlugin');
        }
        /** @var PaymentInterface|null $shipment */
        $payment = $paymentFeeAdjustment->getPayment();
        Assert::notNull($payment);

        $grossValue = $paymentFeeAdjustment->getAmount();
        $taxAdjustment = $this->getPaymentFeeTaxAdjustment($payment, $paymentFeeAdjustment);
        $taxAmount = $taxAdjustment !== null ? $taxAdjustment->getAmount() : 0;
        $netValue = $grossValue - $taxAmount;
        //
        $subTotal = $netValue;
        $total = $subTotal + $taxAmount;

        // Tax rate code
        $taxRateCode = null;

        if ($taxAdjustment) {
            $details = $taxAdjustment->getDetails();
            $taxRateCode = $details['taxRateCode'];
        }


        return $this->lineItemFactory->createWithData(
            $paymentFeeAdjustment->getLabel(),
            1,
            $netValue,
            $subTotal,
            $taxAmount,
            $total,
            0,
            null,
            null,
            $this->getTaxRateFromPaymentFeeAdjustment($taxAdjustment),
            $taxRateCode
        );
    }

    private function getTaxRateFromPaymentFeeAdjustment(?AdjustmentInterface $taxAdjustment): ?string
    {
        if(!$taxAdjustment) {
            return null;
        }

        if(!isset($taxAdjustment->getDetails()['taxRateAmount'])) {
            return null;
        }

        $taxRate = $taxAdjustment->getDetails()['taxRateAmount'];

        return $taxRate * 100 . '%';
    }

    private function getPaymentFeeTaxAdjustment(PaymentInterface $payment, AdjustmentInterface $adjustment): ?AdjustmentInterface
    {
        $order = $payment->getOrder();
        Assert::notNull($order);

        // Find all order tax adjustments
        foreach ($order->getAdjustments(AdjustmentInterface::TAX_ADJUSTMENT) as $taxAdjustment) {
            if (
                $taxAdjustment->getPayment() &&
                $taxAdjustment->getPayment()->getId() == $adjustment->getPayment()->getId()
            ) {
                return $taxAdjustment;
            }
        }

        return null;
    }
}
