<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\EventProducer;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Sylius\InvoicingPlugin\DateTimeProvider;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\InvoiceCreated;
use Symfony\Component\Messenger\MessageBusInterface;

class InvoiceCreatedProducer
{
    private MessageBusInterface $eventBus;

    private DateTimeProvider $dateTimeProvider;

    public function __construct(MessageBusInterface $eventBus, DateTimeProvider $dateTimeProvider)
    {
        $this->eventBus = $eventBus;
        $this->dateTimeProvider = $dateTimeProvider;
    }

    public function postPersist(LifecycleEventArgs $event): void
    {
        $invoice = $event->getEntity();

        if (!$invoice instanceof InvoiceInterface) {
            return;
        }

        $this->dispatchInvoiceCreatedEvent($invoice);
    }

    private function dispatchInvoiceCreatedEvent(InvoiceInterface $invoice): void
    {
        $this->eventBus->dispatch(
            new InvoiceCreated($invoice, $this->dateTimeProvider->__invoke())
        );
    }

}