<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241220000001 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create initial schema for purchase cart service';
    }

    public function up(Schema $schema): void
    {
        // Create products table
        $this->addSql('CREATE TABLE products (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            name VARCHAR(255) NOT NULL, 
            price NUMERIC(10, 2) NOT NULL, 
            vat_rate NUMERIC(5, 4) NOT NULL
        )');

        // Create orders table  
        $this->addSql('CREATE TABLE orders (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            total_price NUMERIC(10, 2) NOT NULL, 
            total_vat NUMERIC(10, 2) NOT NULL, 
            created_at DATETIME NOT NULL --(DC2Type:datetime_immutable)
        )');

        // Create order_items table
        $this->addSql('CREATE TABLE order_items (
            id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, 
            order_id INTEGER NOT NULL, 
            product_id INTEGER NOT NULL, 
            quantity INTEGER NOT NULL, 
            price NUMERIC(10, 2) NOT NULL, 
            vat NUMERIC(10, 2) NOT NULL, 
            CONSTRAINT FK_62809DB18D9F6D38 FOREIGN KEY (order_id) REFERENCES orders (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        )');

        $this->addSql('CREATE INDEX IDX_62809DB18D9F6D38 ON order_items (order_id)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE order_items');
        $this->addSql('DROP TABLE orders');
        $this->addSql('DROP TABLE products');
    }
}