<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250415103029 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE page_comments (id CHAR(36) NOT NULL --(DC2Type:guid)
            , page_id INTEGER NOT NULL, user_id CHAR(36) NOT NULL --(DC2Type:guid)
            , content VARCHAR(1023) NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_DF01910AC4663E4 FOREIGN KEY (page_id) REFERENCES pages (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_DF01910AA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DF01910AC4663E4 ON page_comments (page_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_DF01910AA76ED395 ON page_comments (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pages (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, user_id CHAR(36) NOT NULL --(DC2Type:guid)
            , title VARCHAR(255) NOT NULL, content CLOB DEFAULT NULL, CONSTRAINT FK_2074E575A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_2074E575A76ED395 ON pages (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE role (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, permissions CLOB NOT NULL --(DC2Type:json)
            )
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_configs (user_id CHAR(36) NOT NULL --(DC2Type:guid)
            , theme VARCHAR(50) DEFAULT NULL, notifications_enabled BOOLEAN DEFAULT 1 NOT NULL, language VARCHAR(10) DEFAULT NULL, PRIMARY KEY(user_id), CONSTRAINT FK_1A646639A76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user_statuses (id VARCHAR(2) NOT NULL, name VARCHAR(16) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users (id CHAR(36) NOT NULL --(DC2Type:guid)
            , status_id VARCHAR(2) NOT NULL, email VARCHAR(255) NOT NULL, name VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, remember_token VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, PRIMARY KEY(id), CONSTRAINT FK_1483A5E96BF700BD FOREIGN KEY (status_id) REFERENCES user_statuses (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_1483A5E9E7927C74 ON users (email)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_1483A5E96BF700BD ON users (status_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE role_user (user_id CHAR(36) NOT NULL --(DC2Type:guid)
            , role_id INTEGER NOT NULL, PRIMARY KEY(user_id, role_id), CONSTRAINT FK_332CA4DDA76ED395 FOREIGN KEY (user_id) REFERENCES users (id) NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_332CA4DDD60322AC FOREIGN KEY (role_id) REFERENCES role (id) NOT DEFERRABLE INITIALLY IMMEDIATE)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_332CA4DDA76ED395 ON role_user (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_332CA4DDD60322AC ON role_user (role_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE page_comments
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pages
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE role
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_configs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user_statuses
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE role_user
        SQL);
    }
}
