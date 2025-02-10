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
final class Version20250118030510 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }//end getDescription()

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(
            'CREATE TABLE subscription (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(100) NOT NULL,
                amount INT UNSIGNED NOT NULL,
                balance INT NOT NULL,
                period VARCHAR(255) NOT NULL,
                next_payment_date DATE NOT NULL,
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );

        $this->addSql(
            'CREATE TABLE subscription_account (
                id INT AUTO_INCREMENT NOT NULL,
                subscrip_id INT NOT NULL,
                date DATE NOT NULL,
                amount INT NOT NULL,
                INDEX IDX_E23B63FD6C4A7A3 (subscrip_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE subscription_payment (
                id INT AUTO_INCREMENT NOT NULL,
                subscrip_id INT NOT NULL,
                date DATETIME NOT NULL,
                amount INT NOT NULL,
                INDEX IDX_1E3D6496D6C4A7A3 (subscrip_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE subscription_account ADD CONSTRAINT FK_E23B63FD6C4A7A3
            FOREIGN KEY (subscrip_id) REFERENCES subscription (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE subscription_payment ADD CONSTRAINT FK_1E3D6496D6C4A7A3
            FOREIGN KEY (subscrip_id) REFERENCES subscription (id) ON DELETE CASCADE'
        );
    }//end up()

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE subscription_account DROP FOREIGN KEY FK_E23B63FD6C4A7A3');
        $this->addSql('ALTER TABLE subscription_payment DROP FOREIGN KEY FK_1E3D6496D6C4A7A3');
        $this->addSql('DROP TABLE subscription');
        $this->addSql('DROP TABLE subscription_account');
        $this->addSql('DROP TABLE subscription_payment');
    }//end down()
}//end class
