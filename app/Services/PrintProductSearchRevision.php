<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class PrintProductSearchRevision
{
    public const SETTING_KEY = 'print_product_search_revision';

    public static function bump(): int
    {
        DB::table('settings')->insertOrIgnore([
            'key' => self::SETTING_KEY,
            'display_name' => 'Print product search revision',
            'value' => '1',
            'details' => 'Internal revision used to refresh print product search caches across applications.',
            'type' => 'text',
            'order' => 1,
        ]);

        DB::table('settings')->where('key', self::SETTING_KEY)->increment('value');

        return max(1, (int) DB::table('settings')->where('key', self::SETTING_KEY)->value('value'));
    }

    public static function bumpAfterCommit(): void
    {
        DB::afterCommit(static fn () => self::bump());
    }
}
