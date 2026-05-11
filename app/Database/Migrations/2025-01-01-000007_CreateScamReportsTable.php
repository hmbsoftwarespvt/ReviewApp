<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateScamReportsTable extends Migration
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
            'app_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'description' => [
                'type' => 'TEXT',
            ],
            'risk_level' => [
                'type'       => 'ENUM',
                'constraint' => ['low', 'medium', 'high'],
            ],
            'evidence_urls' => [
                'type' => 'JSON',
                'null' => true,
            ],
            'approval_status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'approved', 'rejected'],
                'default'    => 'pending',
            ],
            'verification_notes' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('app_id');
        $this->forge->addKey('user_id');
        $this->forge->addKey('approval_status');
        $this->forge->addKey('risk_level');
        $this->forge->addKey('created_at');
        
        // Add foreign keys with cascade delete
        $this->forge->addForeignKey('app_id', 'apps', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('scam_reports');
    }

    public function down()
    {
        $this->forge->dropTable('scam_reports');
    }
}
