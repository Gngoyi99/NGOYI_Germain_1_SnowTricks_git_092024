<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241203230050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article ALTER created_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING created_at::timestamp');
        $this->addSql('ALTER TABLE article ALTER updated_at TYPE TIMESTAMP(0) WITHOUT TIME ZONE USING updated_at::timestamp');
        $this->addSql('COMMENT ON COLUMN article.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN article.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE article ALTER created_at TYPE VARCHAR(255)');
        $this->addSql('ALTER TABLE article ALTER updated_at TYPE VARCHAR(255)');
        $this->addSql('COMMENT ON COLUMN article.created_at IS NULL');
        $this->addSql('COMMENT ON COLUMN article.updated_at IS NULL');
    }
}
