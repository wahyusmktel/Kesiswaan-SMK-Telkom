<?php

namespace Tests\Feature;

use App\Models\PilketosDpt;
use App\Models\PilketosKandidat;
use App\Models\PilketosPeriode;
use App\Models\User;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OsisPanelAndBoothTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Use local SQLite database
        Config::set('database.connections.sqlite.database', database_path('database.sqlite'));
        DB::purge('sqlite');
    }

    public function test_landing_page_has_osis_menu(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('/osis');
    }

    public function test_public_osis_pages(): void
    {
        $this->get('/osis')->assertStatus(200);
        $this->get('/osis/live-count')->assertStatus(200);
        $this->get('/osis/api/live-count-data')->assertStatus(200)->assertJson(['status' => 'success']);
        $this->get('/osis/bilik-suara')->assertStatus(200);
    }

    public function test_bilik_suara_scan_and_single_use(): void
    {
        $dpt = PilketosDpt::where('status_memilih', 'belum_memilih')->first();
        if (!$dpt) {
            $this->markTestSkipped('No DPT available');
        }

        // 1. Scan physical 1D barcode
        $scanResponse = $this->postJson('/osis/bilik-suara/scan', [
            'barcode' => $dpt->barcode_token,
            'bilik_id' => 'Bilik Test 01',
        ]);
        $scanResponse->assertStatus(200);
        $scanResponse->assertJson(['status' => 'success']);
        $sessionToken = $scanResponse->json('session_token');
        $this->assertNotEmpty($sessionToken);

        // 2. Submit ballot
        $kandidat = PilketosKandidat::first();
        $submitResponse = $this->postJson('/osis/bilik-suara/submit', [
            'session_token' => $sessionToken,
            'voter_id' => $dpt->id,
            'kandidat_id' => $kandidat->id,
            'bilik_id' => 'Bilik Test 01',
        ]);
        $submitResponse->assertStatus(200);
        $submitResponse->assertJson(['status' => 'success']);

        // 3. Voter must be burned / invalidated
        $dpt->refresh();
        $this->assertEquals('sudah_memilih', $dpt->status_memilih);

        // 4. Re-scan must be rejected (anti-fraud)
        $rescanResponse = $this->postJson('/osis/bilik-suara/scan', [
            'barcode' => $dpt->barcode_token,
            'bilik_id' => 'Bilik Test 01',
        ]);
        $rescanResponse->assertStatus(403);
        $rescanResponse->assertJson(['status' => 'already_voted']);
    }

    public function test_panel_osis_authenticated_access(): void
    {
        $osisUser = User::where('email', 'osis@smktelkom-lpg.sch.id')->first();
        $this->assertNotNull($osisUser);

        $this->actingAs($osisUser);

        $urls = [
            '/panel-osis/dashboard',
            '/panel-osis/profil',
            '/panel-osis/pengurus',
            '/panel-osis/pengurus/create',
            '/panel-osis/agenda',
            '/panel-osis/agenda/create',
            '/panel-osis/pilketos/periode',
            '/panel-osis/pilketos/kandidat',
            '/panel-osis/pilketos/kandidat/create',
            '/panel-osis/pilketos/dpt',
            '/panel-osis/pilketos/dpt/print-cards',
            '/panel-osis/pilketos/rekapitulasi',
            '/panel-osis/pilketos/rekapitulasi/print',
        ];

        foreach ($urls as $url) {
            $response = $this->get($url);
            $response->assertStatus(200);
        }
    }
}
