<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210513110412 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE snooker_break (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, snooker_table_id INT NOT NULL, opponent_id INT NOT NULL, score SMALLINT NOT NULL, INDEX IDX_20A5B99CA76ED395 (user_id), INDEX IDX_20A5B99CE85BFBD7 (snooker_table_id), INDEX IDX_20A5B99C7F656CDC (opponent_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE snooker_break ADD CONSTRAINT FK_20A5B99CA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE snooker_break ADD CONSTRAINT FK_20A5B99CE85BFBD7 FOREIGN KEY (snooker_table_id) REFERENCES snooker_table (id)');
        $this->addSql('ALTER TABLE snooker_break ADD CONSTRAINT FK_20A5B99C7F656CDC FOREIGN KEY (opponent_id) REFERENCES user (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE snooker_break');
    }
}
