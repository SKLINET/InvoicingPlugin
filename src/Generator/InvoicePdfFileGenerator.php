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

namespace Sylius\InvoicingPlugin\Generator;

use Knp\Snappy\GeneratorInterface;
use Sylius\InvoicingPlugin\Entity\InvoiceInterface;
use Sylius\InvoicingPlugin\Model\InvoicePdf;
use Symfony\Component\Config\FileLocatorInterface;
use Twig\Environment;

final class InvoicePdfFileGenerator implements InvoicePdfFileGeneratorInterface
{
    private Environment $templatingEngine;

    private GeneratorInterface $pdfGenerator;

    private FileLocatorInterface $fileLocator;

    private InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator;

    private string $template;

    private string $headerTemplate;

    private string $footerTemplate;

    private string $invoiceLogoPath;

    public function __construct(
        Environment $templatingEngine,
        GeneratorInterface $pdfGenerator,
        FileLocatorInterface $fileLocator,
        InvoiceFileNameGeneratorInterface $invoiceFileNameGenerator,
        string $template,
        string $headerTemplate,
        string $footerTemplate,
        string $invoiceLogoPath
    ) {
        $this->templatingEngine = $templatingEngine;
        $this->pdfGenerator = $pdfGenerator;
        $this->fileLocator = $fileLocator;
        $this->invoiceFileNameGenerator = $invoiceFileNameGenerator;
        $this->template = $template;
        $this->headerTemplate = $headerTemplate;
        $this->footerTemplate = $footerTemplate;
        $this->invoiceLogoPath = $invoiceLogoPath;
    }

    public function generate(InvoiceInterface $invoice): InvoicePdf
    {
        $filename = $this->invoiceFileNameGenerator->generateForPdf($invoice);

        $options = [
            'margin-top' => 20,
            'margin-right' => 20,
            'margin-bottom' => 20,
            'margin-left' => 20,
            // Header html
            'header-html' => $this->templatingEngine->render($this->headerTemplate, [
                'invoice' => $invoice,
                'channel' => $invoice->channel(),
                'invoiceLogoPath' => $this->fileLocator->locate($this->invoiceLogoPath),
            ]),
            // Footer html
            'footer-html' => $this->templatingEngine->render($this->footerTemplate, [
                'invoice' => $invoice,
                'channel' => $invoice->channel(),
                'invoiceLogoPath' => $this->fileLocator->locate($this->invoiceLogoPath),
            ]),
        ];

        $pdf = $this->pdfGenerator->getOutputFromHtml(
            $this->templatingEngine->render($this->template, [
                'invoice' => $invoice,
                'channel' => $invoice->channel(),
                'invoiceLogoPath' => $this->fileLocator->locate($this->invoiceLogoPath),
            ])
            ,
            $options
        );

        return new InvoicePdf($filename, $pdf);
    }
}
