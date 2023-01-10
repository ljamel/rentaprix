<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230106141233 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fixed_fee_calcul DROP FOREIGN KEY FK_385B7E47FFF29ECB');
        $this->addSql('ALTER TABLE fixed_fee_calcul DROP FOREIGN KEY FK_385B7E47841269A9');
        $this->addSql('ALTER TABLE fixed_fee_calcul ADD id INT AUTO_INCREMENT NOT NULL, ADD quantity INT NOT NULL, DROP PRIMARY KEY, ADD PRIMARY KEY (id)');
        $this->addSql('ALTER TABLE fixed_fee_calcul ADD CONSTRAINT FK_385B7E47FFF29ECB FOREIGN KEY (fixed_fee_id) REFERENCES fixed_fee (id)');
        $this->addSql('ALTER TABLE fixed_fee_calcul ADD CONSTRAINT FK_385B7E47841269A9 FOREIGN KEY (calcul_id) REFERENCES calcul (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE fixed_fee_calcul MODIFY id INT NOT NULL');
        $this->addSql('ALTER TABLE fixed_fee_calcul DROP FOREIGN KEY FK_385B7E47841269A9');
        $this->addSql('ALTER TABLE fixed_fee_calcul DROP FOREIGN KEY FK_385B7E47FFF29ECB');
        $this->addSql('DROP INDEX `PRIMARY` ON fixed_fee_calcul');
        $this->addSql('ALTER TABLE fixed_fee_calcul DROP id, DROP quantity');
        $this->addSql('ALTER TABLE fixed_fee_calcul ADD CONSTRAINT FK_385B7E47841269A9 FOREIGN KEY (calcul_id) REFERENCES calcul (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fixed_fee_calcul ADD CONSTRAINT FK_385B7E47FFF29ECB FOREIGN KEY (fixed_fee_id) REFERENCES fixed_fee (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE fixed_fee_calcul ADD PRIMARY KEY (fixed_fee_id, calcul_id)');
    }
}
