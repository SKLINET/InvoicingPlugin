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

use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\InvoicingPlugin\Entity\LineItemInterface;
use Sylius\Component\Core\Model\OrderItemInterface;

interface LineItemFactoryInterface extends FactoryInterface
{
    public function createWithData(
        string $name,
        int $quantity,
        int $unitPrice,
        int $subtotal,
        int $taxTotal,
        int $total,
        int $adjustmentPromotionTotal,
        ?string $variantName = null,
        ?string $variantCode = null,
        ?string $taxRate = null,
        ?string $taxRateCode = null
    ): LineItemInterface;
}
