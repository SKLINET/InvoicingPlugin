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

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\ProductVariant;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\Model\ShipmentInterface;

interface LineItemInterface
{
    public function id();

    public function invoice(): InvoiceInterface;

    public function setInvoice(InvoiceInterface $invoice): void;

    public function name(): string;

    public function variantName(): ?string;

    public function variantCode(): ?string;

    public function quantity(): int;

    public function unitPrice(): int;

    public function discountedUnitPrice(): int;

    public function subtotal(): int;

    public function taxRate(): ?string;

    public function taxRateCode(): ?string;

    public function adjustmentUnitPromotion(): int;

    public function adjustmentPromotionTotal(): int;

    public function taxTotal(): int;

    public function total(): int;

    public function merge(self $newLineItem): void;

    public function compare(self $lineItem): bool;

    public function variant(): ?ProductVariantInterface;

    public function payment(): ?PaymentInterface;

    public function shipment(): ?ShipmentInterface;

}
