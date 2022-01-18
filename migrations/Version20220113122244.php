<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220113122244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SEQUENCE dish_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE ingredient_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE SEQUENCE "user_id_seq" INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE dish (id INT NOT NULL, owner_id INT NOT NULL, name VARCHAR(35) NOT NULL, type VARCHAR(35) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_957D8CB87E3C61F9 ON dish (owner_id)');
        $this->addSql('CREATE TABLE ingredient (id INT NOT NULL, dish_id INT NOT NULL, name VARCHAR(35) NOT NULL, ammount DOUBLE PRECISION DEFAULT NULL, unit VARCHAR(15) DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_6BAF7870148EB0CB ON ingredient (dish_id)');
        $this->addSql('CREATE TABLE "user" (id INT NOT NULL, username VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649F85E0677 ON "user" (username)');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB87E3C61F9 FOREIGN KEY (owner_id) REFERENCES "user" (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE ingredient ADD CONSTRAINT FK_6BAF7870148EB0CB FOREIGN KEY (dish_id) REFERENCES dish (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE ingredient DROP CONSTRAINT FK_6BAF7870148EB0CB');
        $this->addSql('ALTER TABLE dish DROP CONSTRAINT FK_957D8CB87E3C61F9');
        $this->addSql('DROP SEQUENCE dish_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE ingredient_id_seq CASCADE');
        $this->addSql('DROP SEQUENCE "user_id_seq" CASCADE');
        $this->addSql('DROP TABLE dish');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE "user"');
    }
}
