<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAnnouncementsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'course_id' => [
                'type' => 'INT',
                'unsigned' => true,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'created_by' => [
                'type' => 'INT',
                'unsigned' => true,
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
        $this->forge->addKey('course_id');
        $this->forge->addKey('created_by');
        $this->forge->addForeignKey('course_id', 'courses', 'course_id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('created_by', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('announcements');
    }

    public function down()
    {
        $this->forge->dropTable('announcements');
    }
}
