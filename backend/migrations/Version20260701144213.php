<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260701144213 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE appartment (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(64) NOT NULL, description LONGTEXT DEFAULT NULL, price_currency NUMERIC(6, 2) NOT NULL, cleaning_fee_amount NUMERIC(6, 2) NOT NULL, cleaning_fee_currency VARCHAR(20) NOT NULL, last_booked_on_utc DATETIME DEFAULT NULL, amenities JSON NOT NULL, created_at DATETIME DEFAULT NOW(), updated_at DATETIME DEFAULT NOW(), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE appartment');
    }
}
