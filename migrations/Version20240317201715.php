<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20240317201715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE corporations ADD partner_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE corporations ADD CONSTRAINT FK_6F3B37C79393F8FE FOREIGN KEY (partner_id) REFERENCES partners (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_6F3B37C79393F8FE ON corporations (partner_id)');
        $this->addSql('ALTER TABLE partners ADD corporation_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE partners ADD CONSTRAINT FK_EFEB5164B2685369 FOREIGN KEY (corporation_id) REFERENCES corporations (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_EFEB5164B2685369 ON partners (corporation_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE corporations DROP CONSTRAINT FK_6F3B37C79393F8FE');
        $this->addSql('DROP INDEX IDX_6F3B37C79393F8FE');
        $this->addSql('ALTER TABLE corporations DROP partner_id');
        $this->addSql('ALTER TABLE partners DROP CONSTRAINT FK_EFEB5164B2685369');
        $this->addSql('DROP INDEX IDX_EFEB5164B2685369');
        $this->addSql('ALTER TABLE partners DROP corporation_id');
    }
}
