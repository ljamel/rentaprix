<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221017192122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE calcul_user (calcul_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_AF559A91841269A9 (calcul_id), INDEX IDX_AF559A91A76ED395 (user_id), PRIMARY KEY(calcul_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE calcul_user ADD CONSTRAINT FK_AF559A91841269A9 FOREIGN KEY (calcul_id) REFERENCES calcul (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE calcul_user ADD CONSTRAINT FK_AF559A91A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE calcul_user DROP FOREIGN KEY FK_AF559A91841269A9');
        $this->addSql('ALTER TABLE calcul_user DROP FOREIGN KEY FK_AF559A91A76ED395');
        $this->addSql('DROP TABLE calcul_user');
    }
}
