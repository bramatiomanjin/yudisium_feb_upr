<?php

namespace Tests\Feature;

use Tests\TestCase;

class StudentUxAccessibilityTest extends TestCase
{
    public function test_tracking_explains_optional_identifiers_and_revision_requirement(): void
    {
        $view = file_get_contents(resource_path('views/mahasiswa/tracking.blade.php'));

        $this->assertStringContainsString(
            'Masukkan NIM atau Kode Pengajuan untuk melihat status.',
            $view
        );
        $this->assertStringContainsString(
            'Untuk membuka revisi, masukkan keduanya.',
            $view
        );
        $this->assertStringNotContainsString('Kode Pengajuan Yudisium (Opsional)', $view);
        $this->assertStringNotContainsString('NIM wajib diisi', $view);
        $this->assertStringNotContainsString('Kode Pengajuan Yudisium wajib diisi', $view);

    }

    public function test_runtime_status_copy_uses_paraf_pimpinan(): void
    {
        $runtimeFiles = [
            public_path('js/student_tracking.js'),
            public_path('js/yudisium_api.js'),
            public_path('js/admin_dashboard.js'),
            public_path('js/admin_pengajuan.js'),
            public_path('js/admin_detail.js'),
            public_path('js/admin_proses_sk.js'),
        ];

        foreach ($runtimeFiles as $file) {
            $contents = file_get_contents($file);

            $this->assertStringNotContainsString('Tanda Tangan Wakil Dekan', $contents, $file);
        }

        $this->assertStringContainsString(
            '"Paraf Pimpinan"',
            file_get_contents(public_path('js/student_tracking.js'))
        );
    }

    public function test_submission_form_has_readiness_guidance_and_unsaved_change_warning(): void
    {
        $view = file_get_contents(resource_path('views/mahasiswa/pengajuan.blade.php'));
        $script = file_get_contents(public_path('js/student_pengajuan_api_bridge.js'));

        $this->assertStringContainsString('Siapkan sebelum mulai', $view);
        $this->assertStringContainsString('Dokumen persyaratan sudah dipindai dengan jelas.', $view);
        $this->assertStringContainsString('beforeunload', $script);
        $this->assertStringContainsString('hasUnsavedChanges = true', $script);
        $this->assertStringContainsString('hasUnsavedChanges = false', $script);
    }

    public function test_important_modals_expose_accessible_dialog_markup(): void
    {
        $modals = [
            [resource_path('views/mahasiswa/pengajuan.blade.php'), 'successModal'],
            [resource_path('views/mahasiswa/revisi.blade.php'), 'revisionSuccessModal'],
            [resource_path('views/admin/verifikasi.blade.php'), 'verificationPreviewModal'],
            [resource_path('views/admin/review_revisi.blade.php'), 'revisionPreviewModal'],
            [resource_path('views/admin/pengajuan.blade.php'), 'bulkLoncatModal'],
            [resource_path('views/admin/proses_sk.blade.php'), 'skConfirmModal'],
        ];

        foreach ($modals as [$file, $id]) {
            $contents = file_get_contents($file);
            $start = strpos($contents, 'id="'.$id.'"');

            $this->assertNotFalse($start, $id);

            $markup = substr($contents, max(0, $start - 180), 500);
            $this->assertStringContainsString('role="dialog"', $markup, $id);
            $this->assertStringContainsString('aria-modal="true"', $markup, $id);
            $this->assertStringContainsString('aria-labelledby=', $markup, $id);
            $this->assertStringContainsString('tabindex="-1"', $markup, $id);
        }
    }

    public function test_modal_helper_traps_focus_handles_escape_and_restores_focus(): void
    {
        $helper = file_get_contents(public_path('js/modal_accessibility.js'));

        $this->assertStringContainsString('event.key === "Escape"', $helper);
        $this->assertStringContainsString('event.key !== "Tab"', $helper);
        $this->assertStringContainsString('controller.previousFocus.focus()', $helper);
        $this->assertStringContainsString('modal.querySelectorAll(focusableSelector)', $helper);
    }

    public function test_runtime_submission_code_wording_uses_kode_pengajuan_yudisium(): void
    {
        $runtimeFiles = [
            app_path('Http/Controllers/TrackingController.php'),
            resource_path('views/index.blade.php'),
            resource_path('views/mahasiswa/pengajuan.blade.php'),
            resource_path('views/mahasiswa/tracking.blade.php'),
            resource_path('views/mahasiswa/revisi.blade.php'),
            resource_path('views/mahasiswa/detail_tracking.blade.php'),
            resource_path('views/admin/pengajuan.blade.php'),
            resource_path('views/admin/history.blade.php'),
            resource_path('views/admin/proses_sk.blade.php'),
            public_path('js/student_tracking.js'),
            public_path('js/student_pengajuan_api_bridge.js'),
        ];

        foreach ($runtimeFiles as $file) {
            $contents = file_get_contents($file);

            $this->assertStringNotContainsString(
                'Kode SK Yudisium',
                $contents,
                $file
            );
        }

        $this->assertStringContainsString(
            'Kode Pengajuan Yudisium',
            file_get_contents(resource_path('views/index.blade.php'))
        );
    }
}
