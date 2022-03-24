<?php

declare(strict_types=1);

namespace Sylius\InvoicingPlugin\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220324233000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD shipment_id INT DEFAULT NULL, ADD payment_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD CONSTRAINT FK_C91408297BE036FC FOREIGN KEY (shipment_id) REFERENCES sylius_shipment (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item ADD CONSTRAINT FK_C91408294C3A3BB FOREIGN KEY (payment_id) REFERENCES sylius_payment (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_C91408297BE036FC ON sylius_invoicing_plugin_line_item (shipment_id)');
        $this->addSql('CREATE INDEX IDX_C91408294C3A3BB ON sylius_invoicing_plugin_line_item (payment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_C91408294C3A3BB');
        $this->addSql('DROP INDEX IDX_C91408297BE036FC');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP CONSTRAINT FK_C91408294C3A3BB');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP CONSTRAINT FK_C91408297BE036FC');
        $this->addSql('ALTER TABLE sylius_invoicing_plugin_line_item DROP shipment_id, DROP payment_id');
    }
}
