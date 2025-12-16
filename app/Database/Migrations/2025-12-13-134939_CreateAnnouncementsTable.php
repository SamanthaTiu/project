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
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
        ],
        'course_id' => [
            'type' => 'INT',
            'constraint' => 11,
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
            'constraint' => 11,
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

    $this->forge->addPrimaryKey('id');
    $this->forge->addForeignKey('course_id', 'courses', 'course_id', 'CASCADE', 'CASCADE');
    $this->forge->addForeignKey('created_by', 'users', 'user_id', 'CASCADE', 'CASCADE');
    $this->forge->createTable('announcements');
}

public function down()
{
    $this->forge->dropTable('announcements');
}
}
