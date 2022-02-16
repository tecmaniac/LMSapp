<?php

namespace App\Models\costum;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;

class dbCreate extends Model
{
    protected $dbName = 'lmsdb';


    public function dbExists()
    {
        try {
            DB::connection()->getPdo(); //connection check 
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function dbMake($isBlank = false)
    {
        try {
            DB::disconnect('lmsdb');
            Config::set('database.connections.mysql.database', ''); //set blank db first
            DB::statement("CREATE DATABASE " . $this->dbName);
            Config::set('database.connections.mysql.database', $this->dbName);
            DB::reconnect();

            $this->up($isBlank);

            return true;
        } catch (\Exception $ex) {
            return $ex->getMessage();
        }
    }

    public function up($isBlank = true)
    {
        //Boolean type will make TINYINT(1) wh tinyinteger will create TINYINT(4)

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
                $table->integer('user_id', true)->notNull();
                $table->string('username', 20)->unique()->notNull();
                $table->string('password', 256)->notNull();
                $table->string('email', 100)->unique()->notNull();
                $table->boolean('emailverified')->default(false);
                $table->boolean('role')->notNull();
                //0 = God mode, 1 = Power Admin, 2 = Super Admin, 3 = Admin, 4 = User Teacher, 5 = User Student
                $table->string('avatar', 512)->nullable(); //full path
                $table->boolean('status')->nullable();
                //0 = inactive, 1 = active, 2 = locked, 3 = banned
                $table->string('fullname', 100)->nullable();
                $table->string('birthplace', 100)->nullable();
                $table->date('birthday')->nullable();
                $table->boolean('sex')->notNull(); //0 = male, 1 = female
                $table->boolean('religion')->nullable();
                //0 = Moslem, 1 = Catholic, 2 = Christian, 3 = Budish, 4 = Hindu, 5 = Confusian
                $table->string('address', 512)->nullable();
                $table->string('phone', 100)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('schools')) {
            Schema::create('schools', function (Blueprint $table) {
                $table->integer('school_id', true)->notNull();
                $table->string('npsn', 50)->unique()->notNull();
                $table->string('name', 200)->unique()->notNull();
                $table->boolean('schoolstatus')->notNull(); //0 = Negeri, 1 = Swasta
                $table->string('address', 512)->notNull();
                $table->string('email', 100)->notNull();
                $table->string('phone', 100)->notNull();
                $table->binary('profile')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('admins')) {
            Schema::create('admins', function (Blueprint $table) {
                $table->integer('admin_id', true)->notNull();
                $table->integer('school_id')->notNull();
                $table->foreign('school_id')->references('school_id')->on('schools')->onDelete('cascade');
                $table->integer('user_id')->notNull();
                $table->string('posititon', 100)->notNull();
                $table->date('joindate')->notNull();
                $table->date('releaveddate')->nullable();
                $table->boolean('status')->notNull(); //0 = inactive, 1 = active
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('teachers')) {
            Schema::create('teachers', function (Blueprint $table) {
                $table->integer('teacher_id', true)->notNull();
                $table->integer('school_id')->notNull();
                $table->foreign('school_id')->references('school_id')->on('schools')->onDelete('cascade');
                $table->string('nuptk', 50)->unique()->notNull();
                $table->integer('user_id')->notNull();
                $table->date('joindate')->notNull();
                $table->date('releaveddate')->nullable();
                $table->boolean('status')->notNull(); //0 = inactive, 1 = active
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('markings')) {
            Schema::create('markings', function (Blueprint $table) {
                $table->integer('marking_id', true)->notNull();
                $table->integer('school_id')->notNull();
                $table->foreign('school_id')->references('school_id')->on('schools')->onDelete('cascade');
                $table->enum('markletter', ['A', 'B', 'C', 'D', 'E', 'F'])->notNull();
                $table->double('minmark', 5, 2)->notNull()->default(0);
                $table->double('maxmark', 5, 2)->notNull()->default(0);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('parents')) {
            Schema::create('parents', function (Blueprint $table) {
                $table->integer('parent_id', true)->notNull();
                $table->integer('school_id')->notNull();
                $table->foreign('school_id')->references('school_id')->on('schools')->onDelete('cascade');
                $table->integer('user_id_father')->nullable();
                $table->integer('user_id_mother')->nullable();
                $table->string('nik_father', 50)->unique()->nullable();
                $table->string('nik_mother', 50)->unique()->nullable();
                $table->string('fatherjob', 50)->nullable();
                $table->string('motherjob', 50)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('students')) {
            Schema::create('students', function (Blueprint $table) {
                $table->integer('student_id', true)->notNull();
                $table->integer('school_id')->notNull();
                $table->foreign('school_id')->references('school_id')->on('schools')->onDelete('cascade');
                $table->integer('parent_id')->notNull();
                $table->foreign('parent_id')->references('parent_id')->on('parents')->onDelete('cascade');
                $table->string('nisn', 50)->unique()->notNull();
                $table->date('enrollmentdate')->notNull();
                $table->date('releaveddate')->nullable();
                $table->tinyInteger('status')->nullable();
                //0 = inactive, 1 = active, 2 = graduated, 3 = DO
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('classes')) {
            Schema::create('classes', function (Blueprint $table) {
                $table->integer('class_id', true)->notNull();
                $table->integer('school_id')->notNull();
                $table->foreign('school_id')->references('school_id')->on('schools')->onDelete('cascade');
                $table->integer('teacher_id')->notNull();
                $table->foreign('teacher_id')->references('teacher_id')->on('teachers')->onDelete('cascade');
                $table->string('code', 6)->unique()->notNull();
                $table->string('acdyear', 20)->notNull();
                $table->string('semester', 20)->notNull();
                $table->string('title', 100)->notNull();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('materials')) {
            Schema::create('materials', function (Blueprint $table) {
                $table->integer('material_id', true)->notNull();
                $table->integer('class_id')->notNull();
                $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
                $table->string('title', 100)->notNull();
                $table->string('attachment', 512)->nullable(); //fullpath
                $table->string('linkexternal', 1024)->nullable();
                $table->date('releaseddate')->notNull();
                $table->date('expireddate')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('class_markings')) {
            Schema::create('class_markings', function (Blueprint $table) {
                $table->integer('class_marking_id', true)->notNull();
                $table->integer('class_id')->notNull();
                $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
                $table->enum('markletter', ['A', 'B', 'C', 'D', 'E', 'F'])->notNull();
                $table->double('minmark', 5, 2)->notNull()->default(0);
                $table->double('maxmark', 5, 2)->notNull()->default(0);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('mark_components')) {
            Schema::create('mark_components', function (Blueprint $table) {
                $table->integer('mark_component_id', true)->notNull();
                $table->integer('class_id')->notNull();
                $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
                $table->string('component', 100)->notNull();
                $table->double('weighted', 5, 2)->notNull()->default(0);
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('class_students')) {
            Schema::create('class_students', function (Blueprint $table) {
                $table->integer('class_student_id', true)->notNull();
                $table->integer('class_id')->notNull();
                $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
                $table->integer('student_id')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('report')) {
            Schema::create('report', function (Blueprint $table) {
                $table->integer('report_id', true)->notNull();
                $table->integer('class_student_id')->notNull();
                $table->foreign('class_student_id')->references('class_student_id')->on('class_students')->onDelete('cascade');
                $table->double('finalmark', 5, 2)->notNull()->default(0);
                $table->enum('lettermark', ['A', 'B', 'C', 'D', 'E', 'F'])->notNull();
                $table->string('comment', 1024)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('report_detail')) {
            Schema::create('report_detail', function (Blueprint $table) {
                $table->integer('report_detail_id', true)->notNull();
                $table->integer('report_id')->notNull();
                $table->foreign('report_id')->references('report_id')->on('report')->onDelete('cascade');
                $table->string('component', 100)->notNull();
                $table->double('weighted', 5, 2)->notNull()->default(0);
                $table->double('mark', 5, 2)->notNull()->default(0);
                $table->enum('lettermark', ['A', 'B', 'C', 'D', 'E', 'F'])->notNull();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('assignments')) {
            Schema::create('assignments', function (Blueprint $table) {
                $table->integer('assignment_id', true)->notNull();
                $table->integer('class_id')->notNull();
                $table->foreign('class_id')->references('class_id')->on('classes')->onDelete('cascade');
                $table->string('title', 256)->notNull();
                $table->date('assigned')->notNull();
                $table->date('due')->notNull();
                $table->double('maxpoint', 5, 2)->notNull()->default(0);
                $table->double('weighted', 5, 2)->notNull()->default(0);
                $table->tinyInteger('type')->nullable();
                //0 = build assigment, 1 = link submission, 2 = file submission
                $table->string('notes', 1024)->notNull();
                $table->string('attachment', 512)->nullable(); //fullpath
                $table->boolean('counttofinalrepot')->notNull()->default(1); //0 = not counted, 1 = counted
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('assignments_detail')) {
            Schema::create('assignments_detail', function (Blueprint $table) {
                $table->integer('detail_id', true)->notNull();
                $table->integer('assignment_id')->notNull();
                $table->foreign('assignment_id')->references('assignment_id')->on('assignments')->onDelete('cascade');
                $table->integer('student_id')->notNull();
                $table->integer('ans_id')->nullable();
                $table->string('linksubmission', 1024)->nullable();
                $table->string('filesubmission', 1024)->nullable(); //fullpath
                $table->double('mark', 5, 2)->nullable();
                $table->double('finalmark', 5, 2)->nullable();
                $table->enum('lettermark', ['A', 'B', 'C', 'D', 'E', 'F'])->notNull();
                $table->date('submited')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('student_ans')) {
            Schema::create('student_ans', function (Blueprint $table) {
                $table->integer('ans_id', true)->notNull();
                $table->integer('assignment_id')->notNull();
                $table->foreign('assignment_id')->references('assignment_id')->on('assignments')->onDelete('cascade');
                $table->integer('build_id')->notNull();
                $table->integer('student_id')->notNull();
                $table->date('datetaken')->notNull();
                $table->date('datesubmited')->nullable();
                $table->double('finalscore', 5, 2)->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('ans_sections')) {
            Schema::create('ans_sections', function (Blueprint $table) {
                $table->integer('section_id', true)->notNull();
                $table->integer('ans_id')->notNull();
                $table->foreign('ans_id')->references('ans_id')->on('student_ans')->onDelete('cascade');
                $table->tinyInteger('type')->nullable();
                //0 = Multiple Choices, 1 = Matching, 2 = True-false, 3 = Short-ans, 4 = Essay
                $table->tinyInteger('totalnum')->notNull();
                $table->tinyInteger('totalpoint')->notNull();
                $table->tinyInteger('maxpoint')->notNull();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('ans_detail_mc')) {
            Schema::create('ans_detail_mc', function (Blueprint $table) {
                $table->integer('detailmc_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('ans_sections')->onDelete('cascade');
                $table->binary('question')->notNull();
                $table->binary('firstchoice')->notNull();
                $table->binary('secondchoice')->notNull();
                $table->binary('thirdchoice')->notNull();
                $table->binary('fourthchoice')->notNull();
                $table->binary('fifthchoice')->nullable();
                $table->tinyInteger('studentanswer')->nullable();
                $table->tinyInteger('answerkey')->notNull();
                $table->tinyInteger('point')->notNull();
                $table->binary('feedback')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('ans_detail_matching')) {
            Schema::create('ans_detail_matching', function (Blueprint $table) {
                $table->integer('detailmatch_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('ans_sections')->onDelete('cascade');
                $table->binary('statement')->notNull();
                $table->binary('studentmatch')->notNull();
                $table->binary('keymatch')->notNull();
                $table->tinyInteger('point')->notNull();
                $table->binary('feedback')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('ans_detail_tf')) {
            Schema::create('ans_detail_tf', function (Blueprint $table) {
                $table->integer('detailtf_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('ans_sections')->onDelete('cascade');
                $table->binary('statement')->notNull();
                $table->boolean('studentanswer')->nullable();
                $table->boolean('answerkey')->notNull();
                $table->tinyInteger('point')->notNull();
                $table->binary('feedback')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('ans_detail_shortans')) {
            Schema::create('ans_detail_shortans', function (Blueprint $table) {
                $table->integer('detailshort_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('ans_sections')->onDelete('cascade');
                $table->binary('question')->notNull();
                $table->tinyInteger('tofill')->notNull();
                $table->tinyInteger('point')->notNull();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('shortans_detail')) {
            Schema::create('shortans_detail', function (Blueprint $table) {
                $table->integer('detail_id', true)->notNull();
                $table->integer('detailshort_id')->notNull();
                $table->foreign('detailshort_id')->references('detailshort_id')->on('ans_detail_shortans')->onDelete('cascade');
                $table->string('answer', 64)->notNull();
                $table->string('answerkey', 64)->notNull();
                $table->binary('feedback')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('ans_detail_essay')) {
            Schema::create('ans_detail_essay', function (Blueprint $table) {
                $table->integer('essay_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('ans_sections')->onDelete('cascade');
                $table->binary('question')->notNull();
                $table->binary('studentanswer')->nullable();
                $table->binary('feedback')->nullable();
                $table->tinyInteger('maxpoint')->notNull();
                $table->tinyInteger('giventpoint')->notNull();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('assignment_build')) {
            Schema::create('assignment_build', function (Blueprint $table) {
                $table->integer('assignment_build_id', true)->notNull();
                $table->integer('assignment_id')->notNull();
                $table->foreign('assignment_id')->references('assignment_id')->on('assignments')->onDelete('cascade');
                $table->integer('build_id')->notNull();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('builds')) {
            Schema::create('builds', function (Blueprint $table) {
                $table->integer('build_id', true)->notNull();
                $table->integer('teacher_id')->notNull();
                $table->string('notes', 1024)->nullable();
                $table->tinyInteger('totalsection')->notNull();
                $table->tinyInteger('timealocation')->notNull(); //in minutes
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('sections')) {
            Schema::create('sections', function (Blueprint $table) {
                $table->integer('section_id', true)->notNull();
                $table->integer('build_id')->notNull();
                $table->foreign('build_id')->references('build_id')->on('builds')->onDelete('cascade');
                $table->tinyInteger('type')->nullable();
                //0 = Multiple Choices, 1 = Matching, 2 = True-false, 3 = Short-ans, 4 = Essay
                $table->tinyInteger('totalnum')->notNull();
                $table->tinyInteger('totalpoint')->notNull();
                $table->boolean('random')->notNull()->default(false);
                $table->timestamp('created_at')->useCurrent();
            });
        }


        if (!Schema::hasTable('sections_detail_mc')) {
            Schema::create('sections_detail_mc', function (Blueprint $table) {
                $table->integer('detailmc_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('cascade');
                $table->binary('question')->notNull();
                $table->binary('firstchoice')->notNull();
                $table->binary('secondchoice')->notNull();
                $table->binary('thirdchoice')->notNull();
                $table->binary('fourthchoice')->notNull();
                $table->binary('fifthchoice')->nullable();
                $table->boolean('randomchoice')->notNull()->default(false);
                $table->tinyInteger('answerkey')->notNull();
                $table->binary('feedback')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('sections_detail_matching')) {
            Schema::create('sections_detail_matching', function (Blueprint $table) {
                $table->integer('detailmatch_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('cascade');
                $table->binary('statement')->notNull();
                $table->binary('match')->notNull();
                $table->binary('feedback')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('sections_detail_tf')) {
            Schema::create('sections_detail_tf', function (Blueprint $table) {
                $table->integer('detailtf_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('cascade');
                $table->binary('statement')->notNull();
                $table->boolean('answer')->notNull();
                $table->binary('feedback')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('sections_detail_shortans')) {
            Schema::create('sections_detail_shortans', function (Blueprint $table) {
                $table->integer('detailshort_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('cascade');
                $table->binary('question')->notNull();
                $table->tinyInteger('tofill')->notNull();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('sections_shortans_detail')) {
            Schema::create('sections_shortans_detail', function (Blueprint $table) {
                $table->integer('detail_id', true)->notNull();
                $table->integer('detailshort_id')->notNull();
                $table->foreign('detailshort_id')->references('detailshort_id')->on('sections_detail_shortans')->onDelete('cascade');
                $table->string('answer', 64)->notNull();
                $table->binary('feedback')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }

        if (!Schema::hasTable('sections_detail_essay')) {
            Schema::create('sections_detail_essay', function (Blueprint $table) {
                $table->integer('essay_id', true)->notNull();
                $table->integer('section_id')->notNull();
                $table->foreign('section_id')->references('section_id')->on('sections')->onDelete('cascade');
                $table->binary('question')->notNull();
                $table->binary('feedback')->nullable();
                $table->tinyInteger('maxpoint')->notNull();
                $table->timestamp('created_at')->useCurrent();
            });
        }





        // /**
        //  * SET DEFAULT VALUE
        //  */
        // $sql = "INSERT INTO users (username, email, role, password) VALUES 
        //         ('SuperAdmin','superadmin@gmail.com',0,'" . bcrypt('SuperAdmin') . "');";
        // DB::statement($sql);



    }

    public function down($isBlank = false)
    {
        $sql = "DROP DATABASE IF EXISTS " . $this->dbName;
        try {
            DB::statement($sql);
            $this->dbMake($isBlank);
            return true;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}