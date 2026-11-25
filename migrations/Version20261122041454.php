<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261122041454 extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Add Recept';
    }// end getDescription()

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE fns_receipt (
                id INT AUTO_INCREMENT NOT NULL,
                receipt_key VARCHAR(64) NOT NULL,
                kkt_owner VARCHAR(200) DEFAULT NULL,
                fiscal_date DATETIME NOT NULL,
                total_sum INT NOT NULL,
                UNIQUE INDEX UNIQ_9715AE15F803A9E3 (receipt_key),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE fns_receipt_item (
                id INT AUTO_INCREMENT NOT NULL,
                receipt_id INT NOT NULL,
                category_id INT DEFAULT NULL,
                name VARCHAR(300) NOT NULL,
                price INT NOT NULL,
                quantity INT NOT NULL,
                sum INT NOT NULL,
                INDEX IDX_63675E872B5CA896 (receipt_id),
                INDEX IDX_63675E8712469DE2 (category_id),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'CREATE TABLE product_category (
                id INT AUTO_INCREMENT NOT NULL,
                name VARCHAR(100) NOT NULL,
                keywords VARCHAR(500) DEFAULT NULL,
                UNIQUE INDEX UNIQ_CDFC73565E237E06 (name),
                PRIMARY KEY(id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB'
        );
        $this->addSql(
            'ALTER TABLE fns_receipt_item ADD CONSTRAINT FK_63675E872B5CA896
                FOREIGN KEY (receipt_id) REFERENCES fns_receipt (id) ON DELETE CASCADE'
        );
        $this->addSql(
            'ALTER TABLE fns_receipt_item ADD CONSTRAINT FK_63675E8712469DE2
                FOREIGN KEY (category_id) REFERENCES product_category (id) ON DELETE SET NULL'
        );
    }// end up()

    /**
     * {@inheritDoc}
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE fns_receipt_item DROP FOREIGN KEY FK_63675E872B5CA896');
        $this->addSql('ALTER TABLE fns_receipt_item DROP FOREIGN KEY FK_63675E8712469DE2');
        $this->addSql('DROP TABLE fns_receipt');
        $this->addSql('DROP TABLE fns_receipt_item');
        $this->addSql('DROP TABLE product_category');
    }// end down()
}// end class
