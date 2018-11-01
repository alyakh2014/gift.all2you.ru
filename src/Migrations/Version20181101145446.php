<?php declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181101145446 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blogresponse CHANGE username user_id VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE user CHANGE is_active is_active TINYINT(1) NOT NULL, CHANGE is_admin is_admin TINYINT(1) NOT NULL, CHANGE userage userage SMALLINT NOT NULL, CHANGE gender gender VARCHAR(1) NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE blogresponse CHANGE user_id username VARCHAR(255) NOT NULL COLLATE utf8_general_ci');
        $this->addSql('ALTER TABLE user CHANGE is_active is_active TINYINT(1) DEFAULT \'1\' NOT NULL, CHANGE is_admin is_admin TINYINT(1) DEFAULT \'0\' NOT NULL, CHANGE userage userage SMALLINT DEFAULT NULL, CHANGE gender gender VARCHAR(1) DEFAULT NULL COLLATE utf8mb4_unicode_ci');
    }
}
