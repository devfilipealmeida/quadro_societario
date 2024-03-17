<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240317071243 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE SEQUENCE corporations_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE corporations (id INT NOT NULL, responsible_company VARCHAR(255) NOT NULL, cpf VARCHAR(11) NOT NULL, birth_date TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, fantasy_name VARCHAR(255) NOT NULL, cnpj VARCHAR(14) NOT NULL, address VARCHAR(255) NOT NULL, neighborhood VARCHAR(255) NOT NULL, complement VARCHAR(255) DEFAULT NULL, city VARCHAR(255) NOT NULL, state VARCHAR(255) NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, updated_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN corporations.birth_date IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN corporations.created_at IS \'(DC2Type:datetime_immutable)\'');
        $this->addSql('COMMENT ON COLUMN corporations.updated_at IS \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('DROP SEQUENCE corporations_id_seq CASCADE');
        $this->addSql('DROP TABLE corporations');
    }
}
