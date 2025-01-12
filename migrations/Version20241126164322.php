<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241126164322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE article DROP CONSTRAINT fk_23a0e669d86650f');
        $this->addSql('DROP INDEX idx_23a0e669d86650f');
        $this->addSql('ALTER TABLE article RENAME COLUMN user_id_id TO user_id');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E66A76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_23A0E66A76ED395 ON article (user_id)');
        $this->addSql('ALTER TABLE illustration DROP CONSTRAINT fk_d67b9a428f3ec46');
        $this->addSql('DROP INDEX idx_d67b9a428f3ec46');
        $this->addSql('ALTER TABLE illustration RENAME COLUMN article_id_id TO article_id');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT FK_D67B9A427294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_D67B9A427294869C ON illustration (article_id)');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f8f3ec46');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT fk_b6bd307f9d86650f');
        $this->addSql('DROP INDEX idx_b6bd307f9d86650f');
        $this->addSql('DROP INDEX idx_b6bd307f8f3ec46');
        $this->addSql('ALTER TABLE message ADD article_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD user_id INT NOT NULL');
        $this->addSql('ALTER TABLE message DROP article_id_id');
        $this->addSql('ALTER TABLE message DROP user_id_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307F7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT FK_B6BD307FA76ED395 FOREIGN KEY (user_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_B6BD307F7294869C ON message (article_id)');
        $this->addSql('CREATE INDEX IDX_B6BD307FA76ED395 ON message (user_id)');
        $this->addSql('ALTER TABLE video DROP CONSTRAINT fk_7cc7da2c8f3ec46');
        $this->addSql('DROP INDEX idx_7cc7da2c8f3ec46');
        $this->addSql('ALTER TABLE video RENAME COLUMN article_id_id TO article_id');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT FK_7CC7DA2C7294869C FOREIGN KEY (article_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_7CC7DA2C7294869C ON video (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE illustration DROP CONSTRAINT FK_D67B9A427294869C');
        $this->addSql('DROP INDEX IDX_D67B9A427294869C');
        $this->addSql('ALTER TABLE illustration RENAME COLUMN article_id TO article_id_id');
        $this->addSql('ALTER TABLE illustration ADD CONSTRAINT fk_d67b9a428f3ec46 FOREIGN KEY (article_id_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_d67b9a428f3ec46 ON illustration (article_id_id)');
        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E66A76ED395');
        $this->addSql('DROP INDEX IDX_23A0E66A76ED395');
        $this->addSql('ALTER TABLE article RENAME COLUMN user_id TO user_id_id');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT fk_23a0e669d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_23a0e669d86650f ON article (user_id_id)');
        $this->addSql('ALTER TABLE video DROP CONSTRAINT FK_7CC7DA2C7294869C');
        $this->addSql('DROP INDEX IDX_7CC7DA2C7294869C');
        $this->addSql('ALTER TABLE video RENAME COLUMN article_id TO article_id_id');
        $this->addSql('ALTER TABLE video ADD CONSTRAINT fk_7cc7da2c8f3ec46 FOREIGN KEY (article_id_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_7cc7da2c8f3ec46 ON video (article_id_id)');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307F7294869C');
        $this->addSql('ALTER TABLE message DROP CONSTRAINT FK_B6BD307FA76ED395');
        $this->addSql('DROP INDEX IDX_B6BD307F7294869C');
        $this->addSql('DROP INDEX IDX_B6BD307FA76ED395');
        $this->addSql('ALTER TABLE message ADD article_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE message ADD user_id_id INT NOT NULL');
        $this->addSql('ALTER TABLE message DROP article_id');
        $this->addSql('ALTER TABLE message DROP user_id');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f8f3ec46 FOREIGN KEY (article_id_id) REFERENCES article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE message ADD CONSTRAINT fk_b6bd307f9d86650f FOREIGN KEY (user_id_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX idx_b6bd307f9d86650f ON message (user_id_id)');
        $this->addSql('CREATE INDEX idx_b6bd307f8f3ec46 ON message (article_id_id)');
    }
}
