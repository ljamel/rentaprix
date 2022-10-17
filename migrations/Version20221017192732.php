<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221017192732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE salary_calcul (salary_id INT NOT NULL, calcul_id INT NOT NULL, INDEX IDX_609E5BA3B0FDF16E (salary_id), INDEX IDX_609E5BA3841269A9 (calcul_id), PRIMARY KEY(salary_id, calcul_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE salary_calcul ADD CONSTRAINT FK_609E5BA3B0FDF16E FOREIGN KEY (salary_id) REFERENCES salary (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE salary_calcul ADD CONSTRAINT FK_609E5BA3841269A9 FOREIGN KEY (calcul_id) REFERENCES calcul (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE salary_calcul DROP FOREIGN KEY FK_609E5BA3B0FDF16E');
        $this->addSql('ALTER TABLE salary_calcul DROP FOREIGN KEY FK_609E5BA3841269A9');
        $this->addSql('DROP TABLE salary_calcul');
    }
}
