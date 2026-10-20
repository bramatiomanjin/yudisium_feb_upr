<?php

namespace Tests\Feature;

use Tests\TestCase;

class PengumumanImageTest extends TestCase
{
    public function test_missing_announcement_image_falls_back_to_default_poster(): void
    {
        $response = $this->get('/uploads/pengumuman/non_existent_file_12345.jpg');

        $response->assertStatus(200);
        $this->assertStringContainsString('image/jpeg', $response->headers->get('Content-Type'));
    }

    public function test_default_pengumuman_image_exists_and_is_valid(): void
    {
        $this->assertFileExists(public_path('images/default_pengumuman.jpg'));
        $this->assertGreaterThan(1000, filesize(public_path('images/default_pengumuman.jpg')));
    }
}
