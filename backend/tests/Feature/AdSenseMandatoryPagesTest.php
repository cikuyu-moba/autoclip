<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\ContactMessage;

class AdSenseMandatoryPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_privacy_policy_page_loads_and_contains_adsense_disclosures(): void
    {
        $response = $this->get('/privacy-policy');

        $response->assertStatus(200);
        $response->assertSee('Kebijakan Privasi');
        $response->assertSee('Google AdSense');
        $response->assertSee('DART cookie');
        $response->assertSee('https://adssettings.google.com');
        $response->assertSee('cikuyu.rc@gmail.com');
    }

    public function test_privacy_alias_redirects(): void
    {
        $response = $this->get('/privacy');
        $response->assertRedirect('/privacy-policy');
    }

    public function test_disclaimer_page_loads(): void
    {
        $response = $this->get('/disclaimer');

        $response->assertStatus(200);
        $response->assertSee('Disclaimer');
        $response->assertSee('Penafian');
        $response->assertSee('cikuyu.rc@gmail.com');
    }

    public function test_about_us_page_loads(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Tentang Arhana');
        $response->assertSee('Visi Kami');
        $response->assertSee('Misi Kami');
    }

    public function test_contact_page_loads_and_form_submits(): void
    {
        $response = $this->get('/contact');
        $response->assertStatus(200);
        $response->assertSee('Hubungi');
        $response->assertSee('cikuyu.rc@gmail.com');

        $submitResponse = $this->post('/contact', [
            'name' => 'Budi Pengunjung',
            'email' => 'budi@example.com',
            'subject' => 'Pertanyaan Umum & Informasi',
            'message' => 'Halo tim Arhana, saya ingin menanyakan mengenai kerjasama periklanan.',
        ]);

        $submitResponse->assertRedirect('/contact');
        $submitResponse->assertSessionHas('success');

        $this->assertDatabaseHas('contact_messages', [
            'name' => 'Budi Pengunjung',
            'email' => 'budi@example.com',
            'subject' => 'Pertanyaan Umum & Informasi',
        ]);
    }

    public function test_sitemap_includes_mandatory_pages(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertSee(route('privacy'));
        $response->assertSee(route('disclaimer'));
        $response->assertSee(route('about'));
        $response->assertSee(route('contact'));
    }
}
