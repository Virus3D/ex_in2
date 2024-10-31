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
final class Version20241029163631 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }//end getDescription()

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, name VARCHAR(50) NOT NULL, type INT NOT NULL, balance INT NOT NULL, INDEX IDX_161498D312469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE card_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE receipt (id INT AUTO_INCREMENT NOT NULL, card_id INT NOT NULL, date DATETIME NOT NULL, balance INT NOT NULL, comment VARCHAR(100) NOT NULL, INDEX IDX_5399B6454ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE spend (id INT AUTO_INCREMENT NOT NULL, card_id INT NOT NULL, date DATETIME NOT NULL, balance INT NOT NULL, comment VARCHAR(100) NOT NULL, INDEX IDX_ECD2273D4ACC9A20 (card_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE transfer (id INT AUTO_INCREMENT NOT NULL, card_out_id INT NOT NULL, card_in_id INT NOT NULL, date DATETIME NOT NULL, balance INT NOT NULL, INDEX IDX_4034A3C076300B0E (card_out_id), INDEX IDX_4034A3C028CD8FF5 (card_in_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE card ADD CONSTRAINT FK_161498D312469DE2 FOREIGN KEY (category_id) REFERENCES card_category (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE receipt ADD CONSTRAINT FK_5399B6454ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE spend ADD CONSTRAINT FK_ECD2273D4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C076300B0E FOREIGN KEY (card_out_id) REFERENCES card (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE transfer ADD CONSTRAINT FK_4034A3C028CD8FF5 FOREIGN KEY (card_in_id) REFERENCES card (id) ON DELETE CASCADE');
    }//end up()

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE card DROP FOREIGN KEY FK_161498D312469DE2');
        $this->addSql('ALTER TABLE receipt DROP FOREIGN KEY FK_5399B6454ACC9A20');
        $this->addSql('ALTER TABLE spend DROP FOREIGN KEY FK_ECD2273D4ACC9A20');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C076300B0E');
        $this->addSql('ALTER TABLE transfer DROP FOREIGN KEY FK_4034A3C028CD8FF5');
        $this->addSql('DROP TABLE card');
        $this->addSql('DROP TABLE card_category');
        $this->addSql('DROP TABLE receipt');
        $this->addSql('DROP TABLE spend');
        $this->addSql('DROP TABLE transfer');
        $this->addSql('DROP TABLE messenger_messages');
    }//end down()
}//end class
