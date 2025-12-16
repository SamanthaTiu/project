<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAssignmentsTable extends Migration
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
                'null' => false,
            ],
            'title' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => false,
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'due_date' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('course_id', 'courses', 'course_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('assignments');
    }

    public function down()
    {
        $this->forge->dropTable('assignments');
    }
}
