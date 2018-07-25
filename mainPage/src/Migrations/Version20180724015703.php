<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20180724015703 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY id_room_pk');
        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY id_user_pk');
        $this->addSql('ALTER TABLE user_room ADD CONSTRAINT FK_81E1D52A76ED395 FOREIGN KEY (user_id) REFERENCES user (id_user)');
        $this->addSql('ALTER TABLE user_room ADD CONSTRAINT FK_81E1D5254177093 FOREIGN KEY (room_id) REFERENCES room (id_room)');
        $this->addSql('ALTER TABLE user_room RENAME INDEX id_room_pk TO IDX_81E1D5254177093');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D52A76ED395');
        $this->addSql('ALTER TABLE user_room DROP FOREIGN KEY FK_81E1D5254177093');
        $this->addSql('ALTER TABLE user_room ADD CONSTRAINT id_room_pk FOREIGN KEY (room_id) REFERENCES room (id_room) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_room ADD CONSTRAINT id_user_pk FOREIGN KEY (user_id) REFERENCES user (id_user) ON UPDATE CASCADE ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user_room RENAME INDEX idx_81e1d5254177093 TO id_room_pk');
    }
}
