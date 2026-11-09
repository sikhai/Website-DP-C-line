<?php

namespace Tests\Unit;

use App\Services\CustomVoyager;
use App\FormFields\AttributesFormField;
use App\FormFields\CurrencyFormField;
use App\FormFields\VendorAttributesFormField;
use TCG\Voyager\Models\DataRow;
use stdClass;
use Tests\TestCase;
use TCG\Voyager\Facades\Voyager;

class CustomVoyagerTest extends TestCase
{
    protected function makeRow(string $type, string $field = 'test_field', $details = null)
    {
        $row = new DataRow();
        $row->type = $type;
        $row->field = $field;
        $row->details = $details ?? (object) [];
        $row->display_name = ucfirst($field);
        $row->required = 0;

        return $row;
    }

    /**
     * Test standard Voyager form fields can be resolved without "Missing field type" exception.
     */
    public function test_standard_form_fields_can_be_resolved_without_missing_field_type_exception()
    {
        $customVoyager = app('voyager');

        $this->assertInstanceOf(CustomVoyager::class, $customVoyager);

        $testTypes = [
            'text',
            'select_dropdown',
            'image',
            'number',
            'checkbox',
            'text_area',
            'rich_text_box',
            'hidden',
            'time',
            'timestamp',
            'date',
            'file',
            'color',
            'radio_btn',
            'code_editor',
            'markdown_editor',
            'select_multiple',
            'multiple_images',
            'media_picker',
            'multiple_checkbox',
            'coordinates',
        ];

        $dataType = new stdClass();
        $dataTypeContent = new stdClass();

        foreach ($testTypes as $type) {
            $row = $this->makeRow($type);

            // Attempting to render or call formField should not throw "Missing field type: {$type}" Exception
            try {
                $rendered = $customVoyager->formField($row, $dataType, $dataTypeContent);
                $this->assertNotNull($rendered, "Form field [{$type}] returned null");
            } catch (\Exception $e) {
                $this->assertStringNotContainsString("Missing field type: {$type}", $e->getMessage(), "Field [{$type}] failed with missing field type exception");
            }
        }
    }

    /**
     * Test formField with Voyager facade directly.
     */
    public function test_voyager_facade_resolves_text_and_select_dropdown_fields()
    {
        $rowText = $this->makeRow('text');
        $rowSelect = $this->makeRow('select_dropdown');

        $dataType = new stdClass();
        $dataTypeContent = new stdClass();

        $content1 = Voyager::formField($rowText, $dataType, $dataTypeContent);
        $this->assertNotNull($content1);

        $content2 = Voyager::formField($rowSelect, $dataType, $dataTypeContent);
        $this->assertNotNull($content2);
    }

    /**
     * Test custom application form fields registered in AppServiceProvider::boot().
     */
    public function test_custom_app_form_fields_are_registered_and_resolvable()
    {
        $customVoyager = app('voyager');

        // AttributesFormField, VendorAttributesFormField, CurrencyFormField
        $customTypes = ['attributes', 'vendor_attributes', 'currency'];

        $availableFormFields = $customVoyager->formFields();

        foreach ($customTypes as $type) {
            $this->assertTrue(
                $availableFormFields->has($type),
                "Custom form field [{$type}] should be registered in Voyager"
            );
        }

        // Verify instances
        $this->assertInstanceOf(AttributesFormField::class, $availableFormFields->get('attributes'));
        $this->assertInstanceOf(VendorAttributesFormField::class, $availableFormFields->get('vendor_attributes'));
        $this->assertInstanceOf(CurrencyFormField::class, $availableFormFields->get('currency'));
    }

    /**
     * Test invalid field type throws Missing field type Exception with 500 code.
     */
    public function test_invalid_field_type_throws_expected_exception()
    {
        $customVoyager = app('voyager');
        $row = $this->makeRow('non_existent_field_type_xyz');
        $dataType = new stdClass();
        $dataTypeContent = new stdClass();

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing field type: non_existent_field_type_xyz');

        $customVoyager->formField($row, $dataType, $dataTypeContent);
    }

    /**
     * Test Voyager::image resolution parity (Cloudflare R2 and local fallback).
     */
    public function test_image_resolution_logic_works_on_custom_voyager()
    {
        $customVoyager = app('voyager');

        // Products path -> Cloudflare R2
        $r2Url = $customVoyager->image('products/sofa/sample.webp');
        $this->assertEquals('https://cdn.dpceline.com/products/sofa/sample.webp', $r2Url);

        // Non-R2 path -> Local public storage
        $localUrl = $customVoyager->image('settings/site-logo.png');
        $this->assertStringContainsString('/storage/settings/site-logo.png', $localUrl);
        $this->assertStringNotContainsString('cdn.dpceline.com', $localUrl);
    }
}
