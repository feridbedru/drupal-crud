<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210806121143 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entity CHANGE description description VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE field ADD relation_entity_id INT DEFAULT NULL, ADD relation_field_id INT DEFAULT NULL, CHANGE is_nullable is_nullable TINYINT(1) NOT NULL, CHANGE is_on_form is_on_form TINYINT(1) NOT NULL, CHANGE is_on_index is_on_index TINYINT(1) NOT NULL, CHANGE is_on_show is_on_show TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF5455844B85DD9 FOREIGN KEY (relation_entity_id) REFERENCES entity (id)');
        $this->addSql('ALTER TABLE field ADD CONSTRAINT FK_5BF54558632C3D08 FOREIGN KEY (relation_field_id) REFERENCES field (id)');
        $this->addSql('CREATE INDEX IDX_5BF5455844B85DD9 ON field (relation_entity_id)');
        $this->addSql('CREATE INDEX IDX_5BF54558632C3D08 ON field (relation_field_id)');
        $this->addSql('ALTER TABLE field RENAME INDEX idx_7ee5e38881257d5d TO IDX_5BF5455881257D5D');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE entity CHANGE description description VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF5455844B85DD9');
        $this->addSql('ALTER TABLE field DROP FOREIGN KEY FK_5BF54558632C3D08');
        $this->addSql('DROP INDEX IDX_5BF5455844B85DD9 ON field');
        $this->addSql('DROP INDEX IDX_5BF54558632C3D08 ON field');
        $this->addSql('ALTER TABLE field DROP relation_entity_id, DROP relation_field_id, CHANGE is_nullable is_nullable TINYINT(1) DEFAULT NULL, CHANGE is_on_form is_on_form TINYINT(1) DEFAULT NULL, CHANGE is_on_index is_on_index TINYINT(1) DEFAULT NULL, CHANGE is_on_show is_on_show TINYINT(1) DEFAULT NULL');
        $this->addSql('ALTER TABLE field RENAME INDEX idx_5bf5455881257d5d TO IDX_7EE5E38881257D5D');
    }
}
