<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGradesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'assignment_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'student_id' => [
                'type' => 'INT',
                'unsigned' => true,
                'null' => false,
            ],
            'grade' => [
                'type' => 'DECIMAL',
                'constraint' => '5,2',
                'null' => true,
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'graded_at' => [
                'type' => 'DATETIME',
                'default' => 'CURRENT_TIMESTAMP',
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('assignment_id', 'assignments', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('student_id', 'users', 'user_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('grades');
    }

    public function down()
    {
        $this->forge->dropTable('grades');
    }
}
