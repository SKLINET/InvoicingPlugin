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

namespace Sylius\InvoicingPlugin;

use Sylius\Component\Core\Model\OrderInterface;

final class SystemDateTimeProvider implements DateTimeProvider
{
    public function __invoke(OrderInterface $order): \DateTimeInterface
    {
        return new \DateTimeImmutable('now');
    }
}
