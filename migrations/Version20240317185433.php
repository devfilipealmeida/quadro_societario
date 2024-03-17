<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240317185433 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('DROP SEQUENCE partner_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE partners_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE partners (id INT NOT NULL, name VARCHAR(255) NOT NULL, cpf VARCHAR(11) NOT NULL, qualification VARCHAR(255) NOT NULL, entry DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN partners.entry IS \'(DC2Type:date_immutable)\'');
        $this->addSql('DROP TABLE partner');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE partners_id_seq CASCADE');
        $this->addSql('CREATE SEQUENCE partner_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE partner (id INT NOT NULL, name VARCHAR(255) NOT NULL, cpf VARCHAR(11) NOT NULL, qualification VARCHAR(255) NOT NULL, entry DATE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN partner.entry IS \'(DC2Type:date_immutable)\'');
        $this->addSql('DROP TABLE partners');
    }
}
