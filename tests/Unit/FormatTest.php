<?php

namespace Tests\Unit;

use App\Support\Format;
use PHPUnit\Framework\TestCase;

class FormatTest extends TestCase
{
    public function test_mask_nik_hides_the_middle_digits(): void
    {
        $this->assertSame('000000••••••0101', Format::maskNik('0000000000000101'));
        $this->assertSame('Belum dicatat', Format::maskNik('123'));
    }

    public function test_initials_ignore_text_in_parentheses(): void
    {
        $this->assertSame('SA', Format::initials('Siti Aminah (Ketua)'));
        $this->assertSame('K', Format::initials(''));
    }

    public function test_age_counts_birthdays_that_have_not_passed(): void
    {
        $this->assertSame('10 tahun', Format::age(['birthDate' => '2016-11-03'], '2026-11-03'));
        $this->assertSame('9 tahun', Format::age(['birthDate' => '2016-11-04'], '2026-11-03'));
        $this->assertSame('± 12 tahun (perkiraan)', Format::age(['birthDateUnknown' => true, 'approximateAge' => 12], '2026-11-03'));
        $this->assertSame('Usia belum diketahui', Format::age(['birthDate' => ''], '2026-11-03'));
    }

    public function test_living_requires_both_residence_and_active_service(): void
    {
        $this->assertTrue(Format::living(['serviceType' => 'Tinggal di panti', 'residenceStatus' => 'Masih menerima layanan']));
        $this->assertFalse(Format::living(['serviceType' => 'Tinggal di panti', 'residenceStatus' => 'Sudah keluar']));
        $this->assertFalse(Format::living(['serviceType' => 'Layanan nonresidensial', 'residenceStatus' => 'Masih menerima layanan']));
    }

    public function test_coordinates_build_map_links_only_for_numeric_values(): void
    {
        $this->assertFalse(Format::coordinates(null, null)['has']);

        $map = Format::coordinates(-3.4581, 114.8312);
        $this->assertSame('-3.458100, 114.831200', $map['text']);
        $this->assertStringContainsString('openstreetmap.org', $map['embedUrl']);
    }
}
