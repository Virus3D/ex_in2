<?php

/**
 * Expenses/Income.
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
final class Version20260123150000 extends AbstractMigration
{
    /**
     * {@inheritDoc}
     */
    public function getDescription(): string
    {
        return 'Add isActive field to Place table';
    }// end getDescription()

    /**
     * {@inheritDoc}
     */
    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE place ADD is_active TINYINT(1) DEFAULT 1 NOT NULL');
    }// end up()

    /**
     * {@inheritDoc}
     */
    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE place DROP is_active');
    }// end down()
}// end class
