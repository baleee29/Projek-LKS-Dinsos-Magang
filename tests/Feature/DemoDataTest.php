<?php

namespace Tests\Feature;

use App\Support\Demo\DemoAccounts;
use App\Support\Demo\DemoClients;
use App\Support\Demo\DemoComplaints;
use App\Support\Demo\DemoLetters;
use App\Support\Demo\DemoMembers;
use App\Support\Demo\DemoNotifications;
use App\Support\Demo\DemoOrganizations;
use App\Support\Demo\DemoPsmMembers;
use App\Support\Demo\DemoRegistrations;
use App\Support\Demo\DemoReports;
use Tests\TestCase;

class DemoDataTest extends TestCase
{
    public function test_minimum_quantities_are_met(): void
    {
        $this->assertGreaterThanOrEqual(8, DemoOrganizations::raw()->where('pillar', 'lks')->count());
        $this->assertGreaterThanOrEqual(6, DemoOrganizations::raw()->where('pillar', 'karang-taruna')->count());
        $this->assertGreaterThanOrEqual(10, DemoPsmMembers::raw()->count());
        $this->assertGreaterThanOrEqual(40, DemoClients::raw()->count());
        $this->assertGreaterThanOrEqual(15, DemoMembers::raw()->count());
        $this->assertGreaterThanOrEqual(12, DemoReports::raw()->count());
        $this->assertGreaterThanOrEqual(15, DemoLetters::raw()->count());
        $this->assertGreaterThanOrEqual(10, DemoRegistrations::raw()->count());
        $this->assertGreaterThanOrEqual(8, DemoAccounts::raw()->count());
        $this->assertGreaterThanOrEqual(10, DemoComplaints::raw()->count());
        $this->assertGreaterThanOrEqual(15, DemoNotifications::raw()->count());
    }

    public function test_every_report_status_is_present(): void
    {
        $statuses = DemoReports::raw()->pluck('status')->unique();

        foreach (['Draf', 'Terkirim', 'Diajukan', 'Ditinjau', 'Perlu Revisi', 'Disetujui'] as $status) {
            $this->assertTrue($statuses->contains($status), "Status {$status} belum ada.");
        }
    }

    public function test_identity_numbers_are_obviously_fake(): void
    {
        $numbers = DemoClients::raw()->pluck('nik')
            ->merge(DemoClients::raw()->pluck('familyCardNumber'))
            ->merge(DemoMembers::raw()->pluck('nik'))
            ->merge(DemoPsmMembers::raw()->pluck('nik'))
            ->merge(DemoComplaints::raw()->pluck('client_nik'))
            ->filter();

        foreach ($numbers as $number) {
            $this->assertMatchesRegularExpression('/^0{8}\d{8}$/', $number);
        }
    }

    public function test_emails_use_the_example_domain_and_records_point_to_real_parents(): void
    {
        foreach (DemoAccounts::raw() as $account) {
            $this->assertStringEndsWith('@sidaya.example', $account['email']);
        }
        foreach ([DemoClients::raw(), DemoMembers::raw(), DemoReports::raw()] as $rows) {
            foreach ($rows as $row) {
                $this->assertNotNull(DemoOrganizations::find($row['organizationId']));
            }
        }
    }

    public function test_project_has_no_vue_or_inertia(): void
    {
        $composer = (string) file_get_contents(base_path('composer.json'));
        $package = (string) file_get_contents(base_path('package.json'));

        foreach (['vue', 'inertia', 'react', 'livewire', 'tailwind', 'bootstrap'] as $name) {
            $this->assertStringNotContainsStringIgnoringCase($name, $composer);
            $this->assertStringNotContainsStringIgnoringCase($name, $package);
        }
    }
}
