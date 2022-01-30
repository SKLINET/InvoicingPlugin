<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220130233052 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_tax_item DROP CONSTRAINT FK_2951C61C2989F1FD');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP CONSTRAINT FK_C91408292989F1FD');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_invoice CHANGE id id INT AUTO_INCREMENT NOT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item CHANGE invoice_id invoice_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_shop_billing_data ADD company_number VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_tax_item CHANGE invoice_id invoice_id INT NOT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_tax_item ADD CONSTRAINT FK_2951C61C2989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD CONSTRAINT FK_C91408292989F1FD FOREIGN KEY (invoice_id) REFERENCES sylius_invoicing_plugin_invoice (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
    }
}
