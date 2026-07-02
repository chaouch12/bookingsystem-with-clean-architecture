<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260702111529 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appartment ADD price_amount NUMERIC(10, 2) NOT NULL, CHANGE price_currency price_currency VARCHAR(3) NOT NULL, CHANGE cleaning_fee_amount cleaning_fee_amount NUMERIC(10, 2) NOT NULL, CHANGE cleaning_fee_currency cleaning_fee_currency VARCHAR(3) NOT NULL, CHANGE created_at created_at DATETIME DEFAULT NULL, CHANGE updated_at updated_at DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE appartment DROP price_amount, CHANGE created_at created_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE updated_at updated_at DATETIME DEFAULT CURRENT_TIMESTAMP, CHANGE price_currency price_currency NUMERIC(6, 2) NOT NULL, CHANGE cleaning_fee_amount cleaning_fee_amount NUMERIC(6, 2) NOT NULL, CHANGE cleaning_fee_currency cleaning_fee_currency VARCHAR(20) NOT NULL');
    }
}
