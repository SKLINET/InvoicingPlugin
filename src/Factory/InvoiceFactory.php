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

namespace Sylius\InvoicingPlugin\Factory;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceShopBillingDataInterface;

final class InvoiceFactory implements InvoiceFactoryInterface
{
    /**
     * @var string
     * @psalm-var class-string
     */
    private $className;

    private FactoryInterface $invoiceShopBillingDataFactory;

    /**
     * @psalm-param class-string $className
     */
    public function __construct(string $className, FactoryInterface $invoiceShopBillingDataFactory)
    {
        $this->className = $className;
        $this->invoiceShopBillingDataFactory = $invoiceShopBillingDataFactory;
    }

    public function createForData(
        string $number,
        OrderInterface $order,
        \DateTimeInterface $issuedAt,
        BillingDataInterface $billingData,
        string $currencyCode,
        string $localeCode,
        int $total,
        Collection $lineItems,
        Collection $taxItems,
        ChannelInterface $channel,
        string $paymentState,
        InvoiceShopBillingDataInterface $shopBillingData = null,
        \DateTimeInterface $dueDateAt,
        int $adjustmentPromotionTotal
    ): InvoiceInterface {
        return new $this->className(
            $number,
            $order,
            $issuedAt,
            $billingData,
            $currencyCode,
            $localeCode,
            $total,
            $lineItems,
            $taxItems,
            $channel,
            $paymentState,
            $shopBillingData ?? $this->invoiceShopBillingDataFactory->createNew(),
            $dueDateAt,
            $adjustmentPromotionTotal
        );
    }
}
