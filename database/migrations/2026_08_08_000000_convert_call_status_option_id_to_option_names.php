<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Convert stored call_status_option_id values (option ids) to option names
     * so reports can render them without joining dialer_call_status_options.
     *
     * @return void
     */
    public function up()
    {
        $rows = DB::table('feed_contact_valids')
            ->whereNotNull('call_status_option_id')
            ->where('call_status_option_id', '!=', '')
            ->get(['id', 'call_status_option_id']);

        if ($rows->isEmpty()) {
            return;
        }

        $ids = collect($rows)
            ->flatMap(fn ($row) => explode(',', $row->call_status_option_id))
            ->map(fn ($id) => trim((string) $id))
            ->filter(fn ($id) => $id !== '')
            ->unique()
            ->values();

        if ($ids->isEmpty()) {
            return;
        }

        $namesById = DB::table('dialer_call_status_options')
            ->whereIn('id', $ids)
            ->pluck('option', 'id');

        foreach ($rows as $row) {
            $names = collect(explode(',', $row->call_status_option_id))
                ->map(fn ($id) => trim((string) $id))
                ->filter(fn ($id) => $id !== '')
                ->map(fn ($id) => $namesById[$id] ?? null)
                ->filter()
                ->unique()
                ->values();

            if ($names->isEmpty()) {
                continue;
            }

            DB::table('feed_contact_valids')
                ->where('id', $row->id)
                ->update(['call_status_option_id' => $names->implode(',')]);
        }
    }

    /**
     * Restore stored option names back to option ids.
     *
     * @return void
     */
    public function down()
    {
        $rows = DB::table('feed_contact_valids')
            ->whereNotNull('call_status_option_id')
            ->where('call_status_option_id', '!=', '')
            ->get(['id', 'call_status_option_id']);

        if ($rows->isEmpty()) {
            return;
        }

        $names = collect($rows)
            ->flatMap(fn ($row) => explode(',', $row->call_status_option_id))
            ->map(fn ($name) => trim((string) $name))
            ->filter(fn ($name) => $name !== '')
            ->unique()
            ->values();

        if ($names->isEmpty()) {
            return;
        }

        $idsByName = DB::table('dialer_call_status_options')
            ->whereIn('option', $names)
            ->pluck('id', 'option');

        foreach ($rows as $row) {
            $ids = collect(explode(',', $row->call_status_option_id))
                ->map(fn ($name) => trim((string) $name))
                ->filter(fn ($name) => $name !== '')
                ->map(fn ($name) => $idsByName[$name] ?? null)
                ->filter()
                ->unique()
                ->values();

            if ($ids->isEmpty()) {
                continue;
            }

            DB::table('feed_contact_valids')
                ->where('id', $row->id)
                ->update(['call_status_option_id' => $ids->implode(',')]);
        }
    }
};
