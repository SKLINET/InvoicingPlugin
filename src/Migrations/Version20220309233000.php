<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220309233000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_billing_data ADD phone_number VARCHAR(255) DEFAULT NULL, ADD email VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD variant_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD CONSTRAINT FK_C91408293B69A9AF FOREIGN KEY (variant_id) REFERENCES sylius_product_variant (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_billing_data DROP phone_number, DROP email');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_billing_data DROP CONSTRAINT FK_C91408293B69A9AF');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_billing_data DROP variant_id');
    }
}
