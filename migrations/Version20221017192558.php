<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221017192558 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE variable_fee_calcul (variable_fee_id INT NOT NULL, calcul_id INT NOT NULL, INDEX IDX_BA3D7D9024FA2B5D (variable_fee_id), INDEX IDX_BA3D7D90841269A9 (calcul_id), PRIMARY KEY(variable_fee_id, calcul_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE variable_fee_calcul ADD CONSTRAINT FK_BA3D7D9024FA2B5D FOREIGN KEY (variable_fee_id) REFERENCES variable_fee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE variable_fee_calcul ADD CONSTRAINT FK_BA3D7D90841269A9 FOREIGN KEY (calcul_id) REFERENCES calcul (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE variable_fee_calcul DROP FOREIGN KEY FK_BA3D7D9024FA2B5D');
        $this->addSql('ALTER TABLE variable_fee_calcul DROP FOREIGN KEY FK_BA3D7D90841269A9');
        $this->addSql('DROP TABLE variable_fee_calcul');
    }
}
