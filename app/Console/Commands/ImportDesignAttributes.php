<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Design;
use App\Models\Attribute;
use App\Models\AttributeValue;

class ImportDesignAttributes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-design-attributes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // 1. Clear existing data
        \DB::table('entity_attribute_values')->delete(); // nếu có bảng pivot
        \DB::table('attribute_values')->delete();
        \DB::table('attributes')->delete();

        $this->info('Cleared attributes and attribute_values tables.');

        // 2. Proceed with import
        $designs = Design::whereNotNull('attributes')->get();

        foreach ($designs as $design) {
            $attrs = $design->attributes; // nếu cast array
            if (!is_array($attrs)) continue;

            foreach ($attrs as $name => $value) {
                if ($value === null || trim($value) === '') continue;

                // 1) ensure Attribute exists
                $attribute = Attribute::updateOrCreate(
                    ['name' => $name],
                    [
                        'type' => 'design',
                        'status' => false,
                        'value' => ''
                    ]
                );

                // 2) ensure AttributeValue exists
                $attrValue = AttributeValue::firstOrCreate([
                    'attribute_id' => $attribute->id,
                    'value' => (string) $value
                ]);

                // 3) attach to design
                \DB::table('entity_attribute_values')->updateOrInsert([
                    'entity_type' => Design::class,
                    'entity_id'   => $design->id,
                    'attribute_id' => $attribute->id,
                    'attribute_value_id' => $attrValue->id,
                ], [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->info('Import finished');
    }
}
