<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180724014141 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D5254177093');
        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D52A76ED395');
        $this->addSql('CREATE TABLE \'room\' (id_room INT AUTO_INCREMENT NOT NULL, password VARCHAR(30) NOT NULL, type INT NOT NULL, name VARCHAR(20) NOT NULL, INDEX id_room (id_room), PRIMARY KEY(id_room)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE \'teams\' (id_team INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(30) NOT NULL, pixeles VARCHAR(40) NOT NULL, PRIMARY KEY(id_team)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE \'user\' (id_user INT AUTO_INCREMENT NOT NULL, email VARCHAR(50) NOT NULL, nickname VARCHAR(30) NOT NULL, password VARCHAR(100) NOT NULL, INDEX id_user (id_user), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE room');
        $this->addSql('DROP TABLE teams');
        $this->addSql('DROP TABLE user');
        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D5254177093');
        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D52A76ED395');
        $this->addSql('ALTER TABLE user_room ADD CONSTRAINT FK_81E1D5254177093 FOREIGN KEY (room_id) REFERENCES \'room\' (id_room)');
        $this->addSql('ALTER TABLE user_room ADD CONSTRAINT FK_81E1D52A76ED395 FOREIGN KEY (user_id) REFERENCES \'user\' (id_user)');
        $this->addSql('ALTER TABLE user_room RENAME INDEX fk_81e1d5254177093 TO IDX_81E1D5254177093');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D5254177093');
        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D52A76ED395');
        $this->addSql('CREATE TABLE room (id_room INT AUTO_INCREMENT NOT NULL, password VARCHAR(30) NOT NULL COLLATE latin1_swedish_ci, type INT NOT NULL, name VARCHAR(20) NOT NULL COLLATE latin1_swedish_ci, INDEX id_room (id_room), PRIMARY KEY(id_room)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE teams (id_team INT AUTO_INCREMENT NOT NULL, nombre VARCHAR(30) NOT NULL COLLATE latin1_swedish_ci, pixeles VARCHAR(40) NOT NULL COLLATE latin1_swedish_ci, PRIMARY KEY(id_team)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id_user INT AUTO_INCREMENT NOT NULL, email VARCHAR(50) NOT NULL COLLATE latin1_swedish_ci, nickname VARCHAR(30) NOT NULL COLLATE latin1_swedish_ci, password VARCHAR(100) NOT NULL COLLATE latin1_swedish_ci, is_active TINYINT(1) NOT NULL, INDEX id_user (id_user), PRIMARY KEY(id_user)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('DROP TABLE \'room\'');
        $this->addSql('DROP TABLE \'teams\'');
        $this->addSql('DROP TABLE \'user\'');
        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D52A76ED395');
        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D5254177093');
        $this->addSql('ALTER TABLE user_room ADD CONSTRAINT FK_81E1D52A76ED395 FOREIGN KEY (user_id) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE user_room ADD CONSTRAINT FK_81E1D5254177093 FOREIGN KEY (room_id) REFERENCES room (id_room)');
        $this->addSql('ALTER TABLE user_room RENAME INDEX idx_81e1d5254177093 TO FK_81E1D5254177093');
    }
}
