<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Event;

use Sylius\InvoicingPlugin\Entity\InvoiceInterface;

class InvoiceCreated
{
    private $invoice;

    private \DateTimeInterface $date;

    private $createdManually = false;

    public function __construct(InvoiceInterface $invoice, \DateTimeInterface $date, bool $createdManually = false)
    {
        $this->invoice = $invoice;
        $this->date = $date;
        $this->createdManually = $createdManually;
    }

    public function invoice(): InvoiceInterface
    {
        return $this->invoice;
    }

    public function date(): \DateTimeInterface
    {
        return clone $this->date;
    }

    public function isCreatedManually(): bool
    {
        return $this->createdManually;
    }

}