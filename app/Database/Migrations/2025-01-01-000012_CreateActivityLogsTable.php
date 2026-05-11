<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateActivityLogsTable extends Migration
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
                'null'       => false,
            ],
            'activity_type' => [
                'type'       => 'ENUM',
                'constraint' => ['view', 'review', 'scam_report'],
                'null'       => false,
            ],
            'activity_date' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'count' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 1,
                'null'     => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('activity_date');
        $this->forge->addKey('app_id');
        $this->forge->addUniqueKey(['app_id', 'activity_type', 'activity_date'], 'unique_app_activity_date');
        $this->forge->addForeignKey('app_id', 'apps', 'id', 'CASCADE', 'CASCADE');

        $attributes = [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];

        $this->forge->createTable('activity_logs', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('activity_logs', true);
    }
}
