<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201218125611 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE adherent_debtor (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(100) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE invoices (id INT AUTO_INCREMENT NOT NULL, adherent_id INT NOT NULL, debtor_id INT NOT NULL, series VARCHAR(25) NOT NULL, number VARCHAR(255) NOT NULL, issue_date DATE NOT NULL, due_date DATE NOT NULL, currency VARCHAR(3) NOT NULL, requested_amount DECIMAL(10, 2) NOT NULL, paid_amount DECIMAL(10, 2) NOT NULL, balance DECIMAL(10, 2) NOT NULL, invoice_amount DECIMAL(10, 2) NOT NULL, approved_amount DECIMAL(10, 2) DEFAULT NULL, INDEX IDX_9065174425F06C53 (adherent_id), INDEX IDX_90651744B043EC6B (debtor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE invoices ADD CONSTRAINT FK_9065174425F06C53 FOREIGN KEY (adherent_id) REFERENCES adherent_debtor (id)');
        $this->addSql('ALTER TABLE invoices ADD CONSTRAINT FK_90651744B043EC6B FOREIGN KEY (debtor_id) REFERENCES adherent_debtor (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE invoices DROP FOREIGN KEY FK_9065174425F06C53');
        $this->addSql('ALTER TABLE invoices DROP FOREIGN KEY FK_90651744B043EC6B');
        $this->addSql('DROP TABLE adherent_debtor');
        $this->addSql('DROP TABLE invoice');
    }
}
