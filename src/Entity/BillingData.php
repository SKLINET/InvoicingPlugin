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

namespace Sylius\InvoicingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

/** @final */
class BillingData implements BillingDataInterface, ResourceInterface
{
    protected int $id;

    protected string $firstName;

    protected string $lastName;

    protected ?string $company;

    protected string $countryCode;

    protected ?string $provinceCode;

    protected ?string $provinceName;

    protected string $street;

    protected string $city;

    protected string $postcode;

    private ?string $companyNumber;

    private ?string $taxId;

    protected ?string $phoneNumber;

    protected ?string $email;

    public function __construct(
        string $firstName,
        string $lastName,
        string $countryCode,
        string $street,
        string $city,
        string $postcode,
        ?string $provinceCode = null,
        ?string $provinceName = null,
        ?string $company = null,
        ?string $companyNumber = null,
        ?string $taxId = null,
        ?string $phoneNumber = null,
        ?string $email = null
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->countryCode = $countryCode;
        $this->street = $street;
        $this->city = $city;
        $this->postcode = $postcode;
        $this->provinceCode = $provinceCode;
        $this->provinceName = $provinceName;
        $this->company = $company;
        $this->companyNumber = $companyNumber;
        $this->taxId = $taxId;
        $this->phoneNumber = $phoneNumber;
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id();
    }

    public function id(): int
    {
        return $this->id;
    }

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function countryCode(): string
    {
        return $this->countryCode;
    }

    public function street(): string
    {
        return $this->street;
    }

    public function city(): string
    {
        return $this->city;
    }

    public function postcode(): string
    {
        return $this->postcode;
    }

    public function provinceCode(): ?string
    {
        return $this->provinceCode;
    }

    public function provinceName(): ?string
    {
        return $this->provinceName;
    }

    public function company(): ?string
    {
        return $this->company;
    }

    public function companyNumber(): ?string
    {
        return $this->companyNumber;
    }

    public function taxId(): ?string
    {
        return $this->taxId;
    }

    public function phoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function email(): ?string
    {
        return $this->email;
    }

}
