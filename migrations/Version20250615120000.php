<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Add database indexes for performance optimization
 */
final class Version20250615120000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add database indexes for performance optimization';
    }

    public function up(Schema $schema): void
    {
        // Add indexes for frequently queried fields in evaluations table
        // Using underscore naming convention as per Doctrine naming strategy
        $this->addSql('CREATE INDEX IDX_EVAL_AUTHOR ON evaluations (author_id)');
        $this->addSql('CREATE INDEX IDX_EVAL_RECIPIENT ON evaluations (recipient)');
        $this->addSql('CREATE INDEX IDX_EVAL_COURSE ON evaluations (courseid)');
        $this->addSql('CREATE INDEX IDX_EVAL_DATE ON evaluations (date_added)');

        // Add composite indexes for common query patterns
        $this->addSql('CREATE INDEX IDX_EVAL_RECIPIENT_COURSE ON evaluations (recipient, courseid)');
        $this->addSql('CREATE INDEX IDX_EVAL_AUTHOR_DATE ON evaluations (author_id, date_added)');

        // Add index for user email (used in login)
        $this->addSql('CREATE INDEX IDX_USER_EMAIL ON users (email)');

        // Add index for user locale (used in language switching)
        $this->addSql('CREATE INDEX IDX_USER_LOCALE ON users (locale)');

        // Add index for role name (used in role queries)
        $this->addSql('CREATE INDEX IDX_ROLE_NAME ON roles (name)');
    }

    public function down(Schema $schema): void
    {
        // Remove the indexes
        $this->addSql('DROP INDEX IDX_EVAL_AUTHOR ON evaluations');
        $this->addSql('DROP INDEX IDX_EVAL_RECIPIENT ON evaluations');
        $this->addSql('DROP INDEX IDX_EVAL_COURSE ON evaluations');
        $this->addSql('DROP INDEX IDX_EVAL_DATE ON evaluations');
        $this->addSql('DROP INDEX IDX_EVAL_RECIPIENT_COURSE ON evaluations');
        $this->addSql('DROP INDEX IDX_EVAL_AUTHOR_DATE ON evaluations');
        $this->addSql('DROP INDEX IDX_USER_EMAIL ON users');
        $this->addSql('DROP INDEX IDX_USER_LOCALE ON users');
        $this->addSql('DROP INDEX IDX_ROLE_NAME ON roles');
    }
}
