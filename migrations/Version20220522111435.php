<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220522111435 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create_db';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE rules (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE triggere (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, rule_id INT NOT NULL, notification_id INT NOT NULL, INDEX IDX_FA1A3C1CA76ED395 (user_id), UNIQUE INDEX UNIQ_FA1A3C1C744E0351 (rule_id), UNIQUE INDEX UNIQ_FA1A3C1CEF1A9D84 (notification_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE uploads (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, filename VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_scanned TINYINT(1) NOT NULL, scan_passed TINYINT(1) NOT NULL, scan_result LONGTEXT DEFAULT NULL, INDEX IDX_96117F18A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE triggere ADD CONSTRAINT FK_FA1A3C1CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE triggere ADD CONSTRAINT FK_FA1A3C1C744E0351 FOREIGN KEY (rule_id) REFERENCES rules (id)');
        $this->addSql('ALTER TABLE triggere ADD CONSTRAINT FK_FA1A3C1CEF1A9D84 FOREIGN KEY (notification_id) REFERENCES notification (id)');
        $this->addSql('ALTER TABLE uploads ADD CONSTRAINT FK_96117F18A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE triggere DROP FOREIGN KEY FK_FA1A3C1CEF1A9D84');
        $this->addSql('ALTER TABLE triggere DROP FOREIGN KEY FK_FA1A3C1C744E0351');
        $this->addSql('ALTER TABLE triggere DROP FOREIGN KEY FK_FA1A3C1CA76ED395');
        $this->addSql('ALTER TABLE uploads DROP FOREIGN KEY FK_96117F18A76ED395');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE rules');
        $this->addSql('DROP TABLE triggere');
        $this->addSql('DROP TABLE uploads');
        $this->addSql('DROP TABLE user');
    }
}
