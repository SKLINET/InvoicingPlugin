<?php

namespace Sylius\InvoicingPlugin\Ui\Action\Admin;

use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use Webmozart\Assert\Assert;

class OrderInvoicesAction
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private OrderRepositoryInterface $orderRepository;
    private Environment $twig;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        Environment $twig
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->twig = $twig;
    }

    public function __invoke(string $id): Response
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($id);
        Assert::notNull($order);

        //
        $content = $this->twig->render('@SyliusInvoicingPlugin/Order/Admin/_invoices.html.twig', [
            'order' => $order,
            'invoices' => $this->invoiceRepository->findByOrderNumber($order->getNumber())
        ]);

        return new Response($content);
    }

}