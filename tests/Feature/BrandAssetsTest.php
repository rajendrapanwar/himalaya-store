<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandAssetsTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_logo_exists_in_every_size_the_site_uses(): void
    {
        $files = [
            'assets/img/brand/logo-light-64.webp' => [306, 64],
            'assets/img/brand/logo-light-128.webp' => [612, 128],
            'assets/img/brand/logo-dark-64.webp' => [296, 64],
            'assets/img/brand/logo-dark-128.webp' => [591, 128],
            'assets/img/brand/logo-tagline-light-96.webp' => [427, 96],
            'assets/img/brand/logo-tagline-light-192.webp' => [853, 192],
            'assets/img/brand/logo-tagline-dark-96.webp' => [412, 96],
            'assets/img/brand/logo-tagline-dark-192.webp' => [823, 192],
            'assets/img/brand/logo-email.png' => [549, 128],
            'assets/img/brand/logo-print.png' => [853, 192],
            'assets/img/brand/og-logo.png' => [1200, 630],
            'favicon-32.png' => [32, 32],
            'favicon-96.png' => [96, 96],
            'apple-touch-icon.png' => [180, 180],
            'icon-192.png' => [192, 192],
            'icon-512.png' => [512, 512],
            'icon-maskable-512.png' => [512, 512],
        ];

        foreach ($files as $file => $size) {
            $path = public_path($file);
            $this->assertFileExists($path);
            $this->assertSame($size, array_slice(getimagesize($path), 0, 2), "{$file} has the wrong dimensions.");
        }

        // favicon.ico was once an empty file: it must be a real icon holding the 16, 32 and 48 pixel sizes.
        $header = unpack('vreserved/vtype/vcount', (string) file_get_contents(public_path('favicon.ico'), false, null, 0, 6));
        $this->assertSame(['reserved' => 0, 'type' => 1, 'count' => 3], $header);
    }

    public function test_the_web_manifest_only_lists_icons_that_exist(): void
    {
        $manifest = json_decode((string) file_get_contents(public_path('site.webmanifest')), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame('Pahadi Roots', $manifest['name']);
        $this->assertNotEmpty($manifest['icons']);
        foreach ($manifest['icons'] as $icon) {
            [$width, $height] = getimagesize(public_path($icon['src']));
            $this->assertSame($icon['sizes'], "{$width}x{$height}");
        }
    }

    public function test_storefront_pages_show_the_shared_logo_and_icons(): void
    {
        $this->get(route('home'))
            ->assertOk()
            ->assertSee('assets/img/brand/logo-light-64.webp', false)
            ->assertSee('assets/img/brand/logo-dark-64.webp', false)
            ->assertSee('assets/img/brand/logo-tagline-dark-96.webp', false)
            ->assertSee('favicon-32.png', false)
            ->assertSee('apple-touch-icon.png', false)
            ->assertSee('site.webmanifest', false)
            ->assertDontSee('favicon.svg', false);

        $this->get(route('login'))->assertOk()->assertSee('assets/img/brand/logo-dark-64.webp', false);
    }

    public function test_admin_sign_in_and_error_pages_show_the_shared_logo(): void
    {
        $this->get(route('admin.login'))
            ->assertOk()
            ->assertSee('assets/img/brand/logo-dark-64.webp', false)
            ->assertSee('assets/img/brand/logo-light-64.webp', false)
            ->assertSee('favicon-32.png', false);

        $this->get('/a-page-that-does-not-exist')
            ->assertNotFound()
            ->assertSee('assets/img/brand/logo-light-64.webp', false)
            ->assertSee('favicon-32.png', false);
    }
}
