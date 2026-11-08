<?php

namespace Tests\Unit;

use Tests\TestCase;

class StorageImageUrlTest extends TestCase
{
    public function test_storage_image_url_resolves_products_to_r2_cdn()
    {
        $path = 'products/diva/Diva-102-Beige-crop.webp';
        $url = storage_image_url($path);

        $this->assertEquals('https://cdn.dpceline.com/products/diva/Diva-102-Beige-crop.webp', $url);
    }

    public function test_storage_image_url_resolves_storage_prefix_to_r2_cdn()
    {
        $path = 'storage/products/diva/Diva-102-Beige-crop.webp';
        $url = storage_image_url($path);

        $this->assertEquals('https://cdn.dpceline.com/products/diva/Diva-102-Beige-crop.webp', $url);
    }

    public function test_storage_image_url_leaves_external_and_data_urls_untouched()
    {
        $http = 'https://example.com/image.png';
        $data = 'data:image/png;base64,12345';

        $this->assertEquals($http, storage_image_url($http));
        $this->assertEquals($data, storage_image_url($data));
    }

    public function test_storage_image_url_resolves_non_r2_to_local_storage()
    {
        $path = 'settings/logo.png';
        $url = storage_image_url($path);

        $this->assertStringContainsString('/storage/settings/logo.png', $url);
        $this->assertStringNotContainsString('cdn.dpceline.com', $url);
    }

    public function test_voyager_facade_resolves_products_to_r2_cdn()
    {
        $path = 'products/marvel/marvel-opal.webp';
        $url = \TCG\Voyager\Facades\Voyager::image($path);

        $this->assertEquals('https://cdn.dpceline.com/products/marvel/marvel-opal.webp', $url);
    }

    public function test_voyager_facade_resolves_non_r2_to_local_disk()
    {
        $path = 'users/default.png';
        $url = \TCG\Voyager\Facades\Voyager::image($path);

        $this->assertStringContainsString('/storage/users/default.png', $url);
        $this->assertStringNotContainsString('cdn.dpceline.com', $url);
    }
}
