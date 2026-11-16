<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The call tables live on the Asterisk/call-server connection.
     */
    protected $connection = 'mysql-old';

    /**
     * Tables whose comment column now holds an append-only log.
     *
     * @var array<int, string>
     */
    protected array $tables = [
        'callcount',
        'au_callcount_report',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!$this->hasCommentColumn($tableName)) {
                continue;
            }

            // Use raw SQL so the existing table character set is kept.
            DB::connection($this->connection)->statement(
                "ALTER TABLE `{$tableName}` MODIFY `comment` TEXT NULL"
            );
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (!$this->hasCommentColumn($tableName)) {
                continue;
            }

            DB::connection($this->connection)->statement(
                "ALTER TABLE `{$tableName}` MODIFY `comment` VARCHAR(255) NULL"
            );
        }
    }

    private function hasCommentColumn(string $tableName): bool
    {
        return Schema::connection($this->connection)->hasTable($tableName)
            && Schema::connection($this->connection)->hasColumn($tableName, 'comment');
    }
};
