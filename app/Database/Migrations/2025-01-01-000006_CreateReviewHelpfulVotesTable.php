<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateReviewHelpfulVotesTable extends Migration
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
            'review_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        
        // Add unique constraint for user_id + review_id
        $this->forge->addUniqueKey(['user_id', 'review_id'], 'unique_user_review_vote');
        
        // Add foreign keys with cascade delete
        $this->forge->addForeignKey('review_id', 'reviews', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        
        $this->forge->createTable('review_helpful_votes');
    }

    public function down()
    {
        $this->forge->dropTable('review_helpful_votes');
    }
}
