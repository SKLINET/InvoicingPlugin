<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Ui\Action\Admin;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\InvoicingPlugin\Creator\InvoiceCreatorInterface;
use Sylius\InvoicingPlugin\Doctrine\ORM\InvoiceRepositoryInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Event\InvoiceCreated;
use Sylius\InvoicingPlugin\Generator\InvoiceGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;
use Webmozart\Assert\Assert;

class GenerateInvoiceAction
{
    private Session $session;
    private UrlGeneratorInterface $urlGenerator;
    private InvoiceCreatorInterface $invoiceCreator;

    public function __construct(
        InvoiceRepositoryInterface $invoiceRepository,
        OrderRepositoryInterface $orderRepository,
        InvoiceCreatorInterface $invoiceCreator,
        Environment $twig,
        Session $session,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->invoiceRepository = $invoiceRepository;
        $this->orderRepository = $orderRepository;
        $this->twig = $twig;
        $this->session = $session;
        $this->urlGenerator = $urlGenerator;
        $this->invoiceCreator = $invoiceCreator;
    }

    public function __invoke(string $id): Response
    {
        /** @var OrderInterface|null $order */
        $order = $this->orderRepository->find($id);
        Assert::notNull($order);

        try {
            $this->invoiceCreator->__invoke($order->getNumber(), new \DateTime());

            $this->session->getFlashBag()->add(
                'success',
                'sylius_invoicing_plugin.invoice_generate_successfully'
            );
        } catch (\Exception $exception) {
            $this->session->getFlashBag()->add(
                'error',
                $exception->getMessage()
            );
        }

        return new RedirectResponse(
            $this->urlGenerator->generate('sylius_admin_order_show', ['id' => $order->getId()])
        );
    }

}