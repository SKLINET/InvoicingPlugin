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

namespace Sylius\InvoicingPlugin\EventProducer;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Event\OrderPaymentPaid;
use Symfony\Component\Messenger\MessageBusInterface;
use Webmozart\Assert\Assert;

final class OrderPaymentPaidProducer
{
    private MessageBusInterface $eventBus;

    private DateTimeProvider $dateTimeProvider;

    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(
        MessageBusInterface $eventBus,
        DateTimeProvider $dateTimeProvider,
        InvoiceRepositoryInterface $invoiceRepository
    ) {
        $this->eventBus = $eventBus;
        $this->dateTimeProvider = $dateTimeProvider;
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(PaymentInterface $payment): void
    {
        if (!$this->shouldEventBeDispatched($payment)) {
            return;
        }

        $order = $payment->getOrder();

        Assert::notNull($order);

        $this->eventBus->dispatch(new OrderPaymentPaid(
            $order->getNumber(),
            $this->dateTimeProvider->__invoke()
        ));
    }

    private function shouldEventBeDispatched(PaymentInterface $payment): bool
    {
        /** @var OrderInterface $order */
        $order = $payment->getOrder();

        return true;
    }
}
