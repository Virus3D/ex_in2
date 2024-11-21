<?php

/**
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
final class Version20241109123655 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add Place Service';
    }//end getDescription()

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE place (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE place_service (place_id INT NOT NULL, service_id INT NOT NULL, INDEX IDX_D5518D29DA6A219 (place_id), INDEX IDX_D5518D29ED5CA9E6 (service_id), PRIMARY KEY(place_id, service_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_account (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, place_id INT NOT NULL, year INT NOT NULL, month INT NOT NULL, amount INT NOT NULL, INDEX IDX_B2B20438ED5CA9E6 (service_id), INDEX IDX_B2B20438DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_payment (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, place_id INT NOT NULL, year INT NOT NULL, month INT NOT NULL, amount INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_A2ACD691ED5CA9E6 (service_id), INDEX IDX_A2ACD691DA6A219 (place_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE place_service ADD CONSTRAINT FK_D5518D29DA6A219 FOREIGN KEY (place_id) REFERENCES place (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE place_service ADD CONSTRAINT FK_D5518D29ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_account ADD CONSTRAINT FK_B2B20438ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE service_account ADD CONSTRAINT FK_B2B20438DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
        $this->addSql('ALTER TABLE service_payment ADD CONSTRAINT FK_A2ACD691ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE service_payment ADD CONSTRAINT FK_A2ACD691DA6A219 FOREIGN KEY (place_id) REFERENCES place (id)');
    }//end up()

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE place_service DROP FOREIGN KEY FK_D5518D29DA6A219');
        $this->addSql('ALTER TABLE place_service DROP FOREIGN KEY FK_D5518D29ED5CA9E6');
        $this->addSql('ALTER TABLE service_account DROP FOREIGN KEY FK_B2B20438ED5CA9E6');
        $this->addSql('ALTER TABLE service_account DROP FOREIGN KEY FK_B2B20438DA6A219');
        $this->addSql('ALTER TABLE service_payment DROP FOREIGN KEY FK_A2ACD691ED5CA9E6');
        $this->addSql('ALTER TABLE service_payment DROP FOREIGN KEY FK_A2ACD691DA6A219');
        $this->addSql('DROP TABLE place');
        $this->addSql('DROP TABLE place_service');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_account');
        $this->addSql('DROP TABLE service_payment');
    }//end down()
}//end class
