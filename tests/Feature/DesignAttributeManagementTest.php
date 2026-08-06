<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\User;
use Tests\TestCase;

class DesignAttributeManagementTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['app.maintenance_page' => false]);
    }

    public function test_unauthenticated_user_cannot_access_design_attributes()
    {
        $response = $this->getJson(route('design-attributes.index'));
        $response->assertRedirect(route('voyager.login'));

        $attr = Attribute::create(['name' => 'TestAttr_' . uniqid(), 'value' => '', 'type' => 'design', 'status' => 1]);
        try {
            $toggleResponse = $this->postJson(route('design-attributes.toggle-status', ['id' => $attr->id]), [
                'status' => 0,
            ]);
            $toggleResponse->assertRedirect(route('voyager.login'));
        } finally {
            $attr->delete();
        }
    }

    public function test_admin_can_get_design_attributes_list_and_toggle_status()
    {
        $uniqueName1 = 'TestAttrShown_' . uniqid();
        $uniqueName2 = 'TestAttrHidden_' . uniqid();

        $attr1 = Attribute::create(['name' => $uniqueName1, 'value' => '', 'type' => 'design', 'status' => 1]);
        $attr2 = Attribute::create(['name' => $uniqueName2, 'value' => '', 'type' => 'design', 'status' => 0]);

        try {
            $role = \TCG\Voyager\Models\Role::firstOrCreate(['name' => 'admin'], ['display_name' => 'Administrator']);
            $perm = \TCG\Voyager\Models\Permission::firstOrCreate(['key' => 'browse_admin'], ['table_name' => null]);
            $role->permissions()->syncWithoutDetaching([$perm->id]);

            $user = User::first() ?? User::factory()->make(['id' => 1]);
            $user->role_id = $role->id;

            // Test list endpoint
            $response = $this->actingAs($user)->getJson(route('design-attributes.index'));
            $response->assertStatus(200);
            $response->assertJson(['success' => true]);

            $attributes = $response->json('attributes');
            $names = collect($attributes)->pluck('name')->all();
            $this->assertContains($uniqueName1, $names);
            $this->assertContains($uniqueName2, $names);

            // Test toggle status endpoint
            $toggleResponse = $this->actingAs($user)->postJson(route('design-attributes.toggle-status', ['id' => $attr1->id]), [
                'status' => 0,
            ]);
            $toggleResponse->assertStatus(200);
            $toggleResponse->assertJson([
                'success' => true,
                'attribute' => [
                    'id' => $attr1->id,
                    'status' => 0,
                ]
            ]);

            $this->assertEquals(0, $attr1->fresh()->status);
        } finally {
            $attr1->delete();
            $attr2->delete();
        }
    }
}
