<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220524054145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'add_unique_index';
    }

    public function up(Schema $schema): void
    {
        if ($schema->hasTable('rules')) {
            if (!$schema->getTable('rules')->hasUniqueConstraint('rule_name')) {
                $this->addSql('ALTER TABLE `rules` ADD CONSTRAINT rule_name UNIQUE(`name`);');
            }
        }

        if ($schema->hasTable('notification')) {
            if (!$schema->getTable('notification')->hasUniqueConstraint('notification_name')) {
                $this->addSql('ALTER TABLE `notification` ADD CONSTRAINT notification_name UNIQUE(`name`);');
            }
        }

    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs

    }
}
