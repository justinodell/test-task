<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211001124145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE job (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, word_density_id INT DEFAULT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT DEFAULT NULL, status VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_FBD8E0F8A76ED395 (user_id), INDEX IDX_FBD8E0F8F0FAA9A3 (word_density_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, first_name VARCHAR(255) DEFAULT NULL, last_name VARCHAR(255) DEFAULT NULL, email VARCHAR(180) NOT NULL, roles LONGTEXT NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word_density (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, last_job_id INT DEFAULT NULL, url VARCHAR(255) NOT NULL, notes VARCHAR(255) DEFAULT NULL, words_limit INT DEFAULT NULL, words_count INT DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, UNIQUE INDEX UNIQ_CADA1D09F47645AE (url), INDEX IDX_CADA1D09A76ED395 (user_id), INDEX IDX_CADA1D0981E7CC73 (last_job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE word_density_job_result (id INT AUTO_INCREMENT NOT NULL, word_density_id INT DEFAULT NULL, job_id INT DEFAULT NULL, word VARCHAR(255) NOT NULL, word_count INT DEFAULT NULL, word_ratio NUMERIC(11, 10) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, INDEX IDX_E377B86FF0FAA9A3 (word_density_id), INDEX IDX_E377B86FBE04EA9 (job_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE job ADD CONSTRAINT FK_FBD8E0F8F0FAA9A3 FOREIGN KEY (word_density_id) REFERENCES word_density (id)');
        $this->addSql('ALTER TABLE word_density ADD CONSTRAINT FK_CADA1D09A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE word_density ADD CONSTRAINT FK_CADA1D0981E7CC73 FOREIGN KEY (last_job_id) REFERENCES job (id)');
        $this->addSql('ALTER TABLE word_density_job_result ADD CONSTRAINT FK_E377B86FF0FAA9A3 FOREIGN KEY (word_density_id) REFERENCES word_density (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE word_density_job_result ADD CONSTRAINT FK_E377B86FBE04EA9 FOREIGN KEY (job_id) REFERENCES job (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE word_density DROP FOREIGN KEY FK_CADA1D0981E7CC73');
        $this->addSql('ALTER TABLE word_density_job_result DROP FOREIGN KEY FK_E377B86FBE04EA9');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8A76ED395');
        $this->addSql('ALTER TABLE word_density DROP FOREIGN KEY FK_CADA1D09A76ED395');
        $this->addSql('ALTER TABLE job DROP FOREIGN KEY FK_FBD8E0F8F0FAA9A3');
        $this->addSql('ALTER TABLE word_density_job_result DROP FOREIGN KEY FK_E377B86FF0FAA9A3');
        $this->addSql('DROP TABLE job');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE word_density');
        $this->addSql('DROP TABLE word_density_job_result');
    }
}
