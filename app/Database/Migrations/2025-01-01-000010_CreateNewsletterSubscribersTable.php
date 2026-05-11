<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateNewsletterSubscribersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'unsubscribe_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'is_confirmed' => [
                'type'    => 'BOOLEAN',
                'default' => false,
                'null'    => false,
            ],
            'confirmation_token' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'email_count_today' => [
                'type'    => 'INT',
                'default' => 0,
                'null'    => false,
            ],
            'last_email_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'subscribed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'unsubscribed_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->addUniqueKey('unsubscribe_token');
        $this->forge->addKey('is_confirmed');

        $attributes = [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];

        $this->forge->createTable('newsletter_subscribers', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('newsletter_subscribers', true);
    }
}
