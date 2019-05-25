<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190525172106 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        // What we really want to do is create those columns, but allow them to be null... at first. 
        // Then, we can update both fields to today's date. 
        // And, then we can use another ALTER TABLE query to finally make them not null.
        // $this->addSql('ALTER TABLE article ADD created_at DATETIME NOT NULL, ADD updated_at DATETIME NOT NULL');

        $this->addSql('ALTER TABLE article ADD created_at DATETIME DEFAULT NULL, ADD updated_at DATETIME DEFAULT NULL');
        $this->addSql('UPDATE article SET created_at = NOW(), updated_at = NOW()');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE article DROP created_at, DROP updated_at');
    }
}
