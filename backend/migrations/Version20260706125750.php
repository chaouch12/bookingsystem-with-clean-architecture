<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260706125750 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE booking (id INT UNSIGNED AUTO_INCREMENT NOT NULL, appartment_id INT UNSIGNED NOT NULL, guest_user_id INT UNSIGNED NOT NULL, status VARCHAR(16) NOT NULL, created_at DATETIME DEFAULT NULL, updated_at DATETIME DEFAULT NULL, check_in DATE NOT NULL, check_out DATE NOT NULL, guest_count INT UNSIGNED NOT NULL, price_for_period_amount NUMERIC(10, 2) NOT NULL, price_for_period_currency VARCHAR(3) NOT NULL, cleaning_fee_amount NUMERIC(10, 2) NOT NULL, cleaning_fee_currency VARCHAR(3) NOT NULL, amenities_up_charge_amount NUMERIC(10, 2) NOT NULL, amenities_up_charge_currency VARCHAR(3) NOT NULL, total_price_amount NUMERIC(10, 2) NOT NULL, total_price_currency VARCHAR(3) NOT NULL, INDEX IDX_BOOKING_APPARTMENT_ID (appartment_id), INDEX IDX_BOOKING_GUEST_USER_ID (guest_user_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_BOOKING_APPARTMENT FOREIGN KEY (appartment_id) REFERENCES appartment (id)');
        $this->addSql('ALTER TABLE booking ADD CONSTRAINT FK_BOOKING_GUEST_USER FOREIGN KEY (guest_user_id) REFERENCES `user` (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE booking');
    }
}
