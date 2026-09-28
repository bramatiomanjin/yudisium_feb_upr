<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class FrontendXssEscapingTest extends TestCase
{
    public function test_revision_review_escapes_untrusted_dynamic_content(): void
    {
        $source = $this->javascript('admin_review_revisi.js');

        $this->assertStringContainsString('escapeHtml(item.feedback || "-")', $source);
        $this->assertStringContainsString('escapeHtml(item.newFile.name)', $source);
        $this->assertStringContainsString('safeDocumentUrl(', $source);
        $this->assertDoesNotMatchRegularExpression(
            '/\$\{\s*item\.(?:feedback|label|oldValue|newValue)\s*(?:\|\||\})/',
            $source
        );
    }

    public function test_document_renderers_escape_names_and_restrict_preview_urls(): void
    {
        foreach ([
            'admin_detail.js',
            'admin_detail_history.js',
            'admin_verifikasi.js',
        ] as $filename) {
            $source = $this->javascript($filename);

            $this->assertStringContainsString('function escapeHtml(', $source);
            $this->assertStringContainsString('function safeDocumentUrl(', $source);
            $this->assertStringContainsString('url.origin === window.location.origin', $source);
        }

        $this->assertStringNotContainsString(
            '${documentData.filename || "-"}',
            $this->javascript('admin_detail.js')
        );
        $this->assertStringNotContainsString(
            '<strong>${title}</strong>',
            $this->javascript('admin_verifikasi.js')
        );
        $this->assertStringNotContainsString(
            '<span>${filename}</span>',
            $this->javascript('admin_verifikasi.js')
        );
    }

    public function test_submission_table_escapes_dynamic_data_attributes(): void
    {
        $source = $this->javascript('admin_pengajuan.js');

        $this->assertStringContainsString(
            'data-id="${escapeHtml(submission.id)}"',
            $source
        );
        $this->assertStringContainsString(
            'data-status="${escapeHtml(submission.status)}"',
            $source
        );
        $this->assertStringNotContainsString('data-id="${submission.id}"', $source);
        $this->assertStringNotContainsString('data-status="${submission.status}"', $source);
    }

    private function javascript(string $filename): string
    {
        $source = file_get_contents(
            dirname(__DIR__, 2).'/public/js/'.$filename
        );

        $this->assertNotFalse($source);

        return $source;
    }
}
