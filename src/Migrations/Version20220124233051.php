<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220124233051 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice ADD due_date_at DATETIME NOT NULL, ADD adjustment_promotion_total INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD tax_rate_code VARCHAR(255) DEFAULT NULL, ADD adjustment_unit_promotion INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_billing_data ADD tax_id VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_shop_billing_data ADD company_number VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice DROP due_date_at, DROP adjustment_promotion_total');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP tax_rate_code, DROP adjustment_unit_promotion');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_billing_data DROP tax_id');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_shop_billing_data DROP company_number');
    }
}
