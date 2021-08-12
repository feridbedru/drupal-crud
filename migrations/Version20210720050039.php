<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210720050039 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE fields (id INT AUTO_INCREMENT NOT NULL, entity_id INT NOT NULL, name VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, is_nullable TINYINT(1) DEFAULT NULL, type INT NOT NULL, length INT DEFAULT NULL, bydefault VARCHAR(255) DEFAULT NULL, validation VARCHAR(255) DEFAULT NULL, INDEX IDX_7EE5E38881257D5D (entity_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE fields ADD CONSTRAINT FK_7EE5E38881257D5D FOREIGN KEY (entity_id) REFERENCES entity (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE fields');
    }
}
