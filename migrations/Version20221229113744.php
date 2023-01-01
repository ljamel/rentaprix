<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221229113744 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE `user` ADD `subscribe_id` VARCHAR(255) NULL AFTER `password`, ADD `registration_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `subscribe_id`, `user` ADD `connection_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `registration_date`, `user` CHANGE `connection_date` `connection_date` DATE NOT NULL, `user` CHANGE `registration_date` `registration_date` DATE NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP subscribe_id, DROP registration_date, DROP connection_date');
    }
}
