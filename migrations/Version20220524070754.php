<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220524070754 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE triggere DROP INDEX UNIQ_FA1A3C1C744E0351, ADD INDEX IDX_FA1A3C1C744E0351 (rule_id)');
        $this->addSql('ALTER TABLE triggere DROP INDEX UNIQ_FA1A3C1CEF1A9D84, ADD INDEX IDX_FA1A3C1CEF1A9D84 (notification_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE triggere DROP INDEX IDX_FA1A3C1C744E0351, ADD UNIQUE INDEX UNIQ_FA1A3C1C744E0351 (rule_id)');
        $this->addSql('ALTER TABLE triggere DROP INDEX IDX_FA1A3C1CEF1A9D84, ADD UNIQUE INDEX UNIQ_FA1A3C1CEF1A9D84 (notification_id)');
    }
}
