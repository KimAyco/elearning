<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_grades_immutable_before_update');

        DB::unprepared(<<<SQL
CREATE TRIGGER trg_grades_immutable_before_update
BEFORE UPDATE ON grades
FOR EACH ROW
BEGIN
    IF OLD.status IN ('registrar_finalized', 'released') THEN
        IF NEW.status = 'released'
            AND OLD.status = 'registrar_finalized'
            AND NEW.grade_value <=> OLD.grade_value
            AND NEW.submitted_remarks <=> OLD.submitted_remarks
            AND NEW.dean_decision_remarks <=> OLD.dean_decision_remarks
            AND NEW.released_at IS NOT NULL
            AND (OLD.released_at IS NULL OR NEW.released_at <> OLD.released_at)
        THEN
            SET NEW.released_at = NEW.released_at;
        ELSE
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Finalized grades are immutable';
        END IF;
    END IF;
END
SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::unprepared('DROP TRIGGER IF EXISTS trg_grades_immutable_before_update');
    }
};
