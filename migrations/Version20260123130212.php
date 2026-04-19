<?php

/**
 * Expenses/Income
 *
 * @license Shareware
 * @copyright (c) 2024 Virus3D
 */

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260123130212 extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Add service meter';
    }// end getDescription()

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE service_meter_reading (
                id INT AUTO_INCREMENT NOT NULL,
                service_id INT NOT NULL,
                place_id INT NOT NULL,
                year INT NOT NULL,
                month INT NOT NULL,
                reading INT NOT NULL,
                INDEX IDX_51F59A7BED5CA9E6 (service_id),
                INDEX IDX_51F59A7BDA6A219 (place_id),
                UNIQUE INDEX unique_service_place_year_month (service_id, place_id, year, month),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE service_meter_reading ADD CONSTRAINT FK_51F59A7BED5CA9E6
            FOREIGN KEY (service_id) REFERENCES service (id)'
        );
        $this->addSql(
            'ALTER TABLE service_meter_reading ADD CONSTRAINT FK_51F59A7BDA6A219
            FOREIGN KEY (place_id) REFERENCES place (id)'
        );
        $this->addSql('ALTER TABLE service ADD has_meter TINYINT(1) DEFAULT 0 NOT NULL');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0 ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E0E3BD61CE ON messenger_messages');
        $this->addSql('DROP INDEX IDX_75EA56E016BA31DB ON messenger_messages');
        $this->addSql(
            'CREATE INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750
                ON messenger_messages (queue_name, available_at, delivered_at, id)'
        );
    }// end up()

    /**
     * {@inheritDoc}
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE service_meter_reading DROP FOREIGN KEY FK_51F59A7BED5CA9E6');
        $this->addSql('ALTER TABLE service_meter_reading DROP FOREIGN KEY FK_51F59A7BDA6A219');
        $this->addSql('DROP TABLE service_meter_reading');
        $this->addSql('DROP INDEX IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750 ON messenger_messages');
        $this->addSql('CREATE INDEX IDX_75EA56E0FB7336F0 ON messenger_messages (queue_name)');
        $this->addSql('CREATE INDEX IDX_75EA56E0E3BD61CE ON messenger_messages (available_at)');
        $this->addSql('CREATE INDEX IDX_75EA56E016BA31DB ON messenger_messages (delivered_at)');
        $this->addSql('ALTER TABLE service DROP has_meter');
    }// end down()
}// end class
