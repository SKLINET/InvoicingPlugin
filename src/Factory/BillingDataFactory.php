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

use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;
use Sylius\InvoicingPlugin\Entity\BillingData;
use Sylius\InvoicingPlugin\Entity\BillingDataInterface;

final class BillingDataFactory implements BillingDataFactoryInterface
{
    private string $className;

    public function __construct(string $className)
    {
        if (!is_a($className, BillingData::class, true)) {
            throw new \DomainException(
                sprintf(
                    'This factory requires %s or its descend to be used as billing data resource',
                    BillingData::class
                )
            );
        }

        $this->className = $className;
    }

    public function createNew()
    {
        throw new UnsupportedMethodException('createNew');
    }

    public function createFromAddress(AddressInterface $address): BillingDataInterface
    {
        return new $this->className(
            $address->getFirstName(),
            $address->getLastName(),
            $address->getCountryCode(),
            $address->getStreet(),
            $address->getCity(),
            $address->getPostcode(),
            $address->getProvinceCode(),
            $address->getProvinceName(),
            $address->getCompany(),
            // CompanyId
            $this->getCompanyNumber($address),
            // taxId
            $this->getCompanyTaxId($address)
        );
    }

    private function getCompanyNumber(AddressInterface $address): ?string
    {
        $customer = $address->getCustomer();

        if (method_exists($customer, 'getCompanyNumber')) {
            return $customer->getCompanyNumber();
        }

        return null;
    }

    private function getCompanyTaxId(AddressInterface $address): ?string
    {
        $customer = $address->getCustomer();

        if (method_exists($customer, 'getCompanyTaxNumber')) {
            return $customer->getCompanyTaxNumber();
        }

        return null;
    }
}
