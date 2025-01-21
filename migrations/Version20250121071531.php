<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250121071531 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_payment ADD card_id INT NOT NULL');
        $this->addSql('ALTER TABLE subscription_payment ADD CONSTRAINT FK_1E3D64964ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_1E3D64964ACC9A20 ON subscription_payment (card_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_payment DROP FOREIGN KEY FK_1E3D64964ACC9A20');
        $this->addSql('DROP INDEX IDX_1E3D64964ACC9A20 ON subscription_payment');
        $this->addSql('ALTER TABLE subscription_payment DROP card_id');
    }
}
