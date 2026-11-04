<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tables whose comments column now holds an append-only log.
     *
     * @var array<int, string>
     */
    protected array $tables = [
        'feed_contact_valids',
        'feed_contact_valids_report',
    ];

    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'comments')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->text('comments')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'comments')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) {
                $table->string('comments', 255)->nullable()->change();
            });
        }
    }
};
