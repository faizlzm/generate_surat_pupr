<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\AuditReferenceGroup;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeneratorConfigPersistenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_renders_generator_with_boot_config(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('GENERATOR_BOOT', false);
        $response->assertSee('Buat dokumen Word');
    }

    public function test_can_save_and_reload_settings(): void
    {
        $payload = [
            'ministry' => 'KEMENTERIAN TEST',
            'agency' => 'DIREKTORAT TEST',
            'address' => 'Alamat Test',
            'signTitle' => 'Pejabat Test',
            'signName' => 'Nama Test',
            'cc' => "Tembusan Satu\nTembusan Dua",
            'sender' => 'Pengirim Test',
            'subject' => 'Teguran Tertulis',
        ];

        $res = $this->postJson('/generator/simpan-pengaturan', $payload);
        $res->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseHas('app_settings', ['key' => 'general']);

        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee('KEMENTERIAN TEST', false);
        $home->assertSee('Nama Test', false);
        $home->assertSee('Tembusan Satu', false);
    }

    public function test_can_save_and_reload_audit_groups(): void
    {
        $payload = [
            'groups' => [
                ['id' => 'g1', 'name' => 'Grup A', 'references' => ['Ref A1', 'Ref A2']],
                ['id' => 'g2', 'name' => 'Grup B', 'references' => ['Ref B1']],
            ],
        ];

        $res = $this->postJson('/generator/simpan-kelompok-audit', $payload);
        $res->assertOk()->assertJson(['ok' => true]);
        $this->assertDatabaseCount('audit_reference_groups', 2);

        $home = $this->get('/');
        $home->assertOk();
        $home->assertSee('Grup A', false);
        $home->assertSee('Ref A1', false);
        $home->assertSee('Grup B', false);
    }

    public function test_legacy_routes_redirect_to_home(): void
    {
        $this->get('/letters')->assertRedirect('/');
        $this->get('/letters/create')->assertRedirect('/');
        $this->get('/settings')->assertRedirect('/');
    }
}
