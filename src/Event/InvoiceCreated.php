<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Event;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

class InvoiceCreated
{
    private $invoice;

    private \DateTimeInterface $date;

    public function __construct(InvoiceInterface $invoice, \DateTimeInterface $date)
    {
        $this->invoice = $invoice;
        $this->date = $date;
    }

    public function invoice(): InvoiceInterface
    {
        return $this->invoice;
    }

    public function date(): \DateTimeInterface
    {
        return clone $this->date;
    }
}