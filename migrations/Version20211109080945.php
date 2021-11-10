<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211109080945 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE comment (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, movie_id INTEGER NOT NULL, content CLOB NOT NULL, created DATETIME NOT NULL)');
        $this->addSql('CREATE INDEX IDX_9474526CF675F31B ON comment (author_id)');
        $this->addSql('CREATE INDEX IDX_9474526C8F93B6FC ON comment (movie_id)');
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, name, image FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, author_id INTEGER NOT NULL, name VARCHAR(255) NOT NULL COLLATE BINARY, image VARCHAR(255) NOT NULL COLLATE BINARY, CONSTRAINT FK_1D5EF26FF675F31B FOREIGN KEY (author_id) REFERENCES user (id) NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO movie (id, name, image) SELECT id, name, image FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
        $this->addSql('CREATE INDEX IDX_1D5EF26FF675F31B ON movie (author_id)');
        $this->addSql('DROP INDEX IDX_C8A29DD430602CA9');
        $this->addSql('DROP INDEX IDX_C8A29DD48F93B6FC');
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie_kind AS SELECT movie_id, kind_id FROM movie_kind');
        $this->addSql('DROP TABLE movie_kind');
        $this->addSql('CREATE TABLE movie_kind (movie_id INTEGER NOT NULL, kind_id INTEGER NOT NULL, PRIMARY KEY(movie_id, kind_id), CONSTRAINT FK_C8A29DD48F93B6FC FOREIGN KEY (movie_id) REFERENCES movie (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE, CONSTRAINT FK_C8A29DD430602CA9 FOREIGN KEY (kind_id) REFERENCES kind (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE)');
        $this->addSql('INSERT INTO movie_kind (movie_id, kind_id) SELECT movie_id, kind_id FROM __temp__movie_kind');
        $this->addSql('DROP TABLE __temp__movie_kind');
        $this->addSql('CREATE INDEX IDX_C8A29DD430602CA9 ON movie_kind (kind_id)');
        $this->addSql('CREATE INDEX IDX_C8A29DD48F93B6FC ON movie_kind (movie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE comment');
        $this->addSql('DROP INDEX IDX_1D5EF26FF675F31B');
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie AS SELECT id, name, image FROM movie');
        $this->addSql('DROP TABLE movie');
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, image VARCHAR(255) NOT NULL)');
        $this->addSql('INSERT INTO movie (id, name, image) SELECT id, name, image FROM __temp__movie');
        $this->addSql('DROP TABLE __temp__movie');
        $this->addSql('DROP INDEX IDX_C8A29DD48F93B6FC');
        $this->addSql('DROP INDEX IDX_C8A29DD430602CA9');
        $this->addSql('CREATE TEMPORARY TABLE __temp__movie_kind AS SELECT movie_id, kind_id FROM movie_kind');
        $this->addSql('DROP TABLE movie_kind');
        $this->addSql('CREATE TABLE movie_kind (movie_id INTEGER NOT NULL, kind_id INTEGER NOT NULL, PRIMARY KEY(movie_id, kind_id))');
        $this->addSql('INSERT INTO movie_kind (movie_id, kind_id) SELECT movie_id, kind_id FROM __temp__movie_kind');
        $this->addSql('DROP TABLE __temp__movie_kind');
        $this->addSql('CREATE INDEX IDX_C8A29DD48F93B6FC ON movie_kind (movie_id)');
        $this->addSql('CREATE INDEX IDX_C8A29DD430602CA9 ON movie_kind (kind_id)');
    }
}
