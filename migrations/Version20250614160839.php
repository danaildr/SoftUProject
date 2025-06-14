<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250614160839 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE courses (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, UNIQUE INDEX UNIQ_A9A55A4C5E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE evaluations (id INT AUTO_INCREMENT NOT NULL, value DOUBLE PRECISION NOT NULL, comment LONGTEXT NOT NULL, date_added DATETIME NOT NULL, author_id INT NOT NULL, recipient INT NOT NULL, courseid INT NOT NULL, authorId INT DEFAULT NULL, recepientId INT DEFAULT NULL, coursesId INT DEFAULT NULL, INDEX IDX_3B72691DA196F9FD (authorId), INDEX IDX_3B72691D76C6E161 (recepientId), INDEX IDX_3B72691DFDCF52B4 (coursesId), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE roles (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_B63E2EC75E237E06 (name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT '(DC2Type:json)', password VARCHAR(255) NOT NULL, full_name VARCHAR(255) NOT NULL, city VARCHAR(255) DEFAULT NULL, address VARCHAR(255) DEFAULT NULL, phone VARCHAR(20) DEFAULT NULL, birthday DATE DEFAULT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users_roles (user_id INT NOT NULL, role_id INT NOT NULL, INDEX IDX_51498A8EA76ED395 (user_id), INDEX IDX_51498A8ED60322AC (role_id), PRIMARY KEY(user_id, role_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluations ADD CONSTRAINT FK_3B72691DA196F9FD FOREIGN KEY (authorId) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluations ADD CONSTRAINT FK_3B72691D76C6E161 FOREIGN KEY (recepientId) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluations ADD CONSTRAINT FK_3B72691DFDCF52B4 FOREIGN KEY (coursesId) REFERENCES courses (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8EA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_roles ADD CONSTRAINT FK_51498A8ED60322AC FOREIGN KEY (role_id) REFERENCES roles (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluations DROP FOREIGN KEY FK_3B72691DA196F9FD
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluations DROP FOREIGN KEY FK_3B72691D76C6E161
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE evaluations DROP FOREIGN KEY FK_3B72691DFDCF52B4
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8EA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_roles DROP FOREIGN KEY FK_51498A8ED60322AC
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE courses
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE evaluations
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE roles
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE user
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users_roles
        SQL);
    }
}
