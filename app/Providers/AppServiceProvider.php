<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $response = static function (string $message): callable {
            return static function (Request $request, array $headers) use ($message) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                    ], 429, $headers);
                }

                return response($message, 429, $headers);
            };
        };

        $key = static fn (string ...$parts): string => hash(
            'sha256',
            implode('|', array_map('strtolower', $parts))
        );

        RateLimiter::for('login', function (Request $request) use ($key, $response): array {
            $identity = (string) $request->input('email', 'anonymous');

            return [
                Limit::perMinute(5)
                    ->by('login-identity:'.$key($identity, $request->ip()))
                    ->response($response('Terlalu banyak percobaan login. Silakan coba lagi sebentar.')),
                Limit::perMinute(60)
                    ->by('login-ip:'.$key($request->ip()))
                    ->response($response('Terlalu banyak percobaan login. Silakan coba lagi sebentar.')),
            ];
        });

        RateLimiter::for('admin-register', fn (Request $request): Limit => Limit::perHour(20)
            ->by('admin-register:'.$key($request->ip()))
            ->response($response('Terlalu banyak permintaan registrasi. Silakan coba lagi nanti.')));

        RateLimiter::for('tracking', fn (Request $request): Limit => Limit::perMinute(60)
            ->by('tracking:'.$key($request->ip()))
            ->response($response('Terlalu banyak permintaan tracking. Silakan coba lagi sebentar.')));

        RateLimiter::for('submission', function (Request $request) use ($key, $response): array {
            $nim = (string) $request->input('nim', 'anonymous');

            return [
                Limit::perHour(10)
                    ->by('submission-nim:'.$key($nim, $request->ip()))
                    ->response($response('Terlalu banyak percobaan pengajuan. Silakan coba lagi nanti.')),
                Limit::perHour(60)
                    ->by('submission-ip:'.$key($request->ip()))
                    ->response($response('Terlalu banyak percobaan pengajuan. Silakan coba lagi nanti.')),
            ];
        });

        RateLimiter::for('revision-access', function (Request $request) use ($key, $response): array {
            $nim = (string) $request->input('nim', 'anonymous');
            $code = (string) $request->input('kode_pengajuan', 'anonymous');

            return [
                Limit::perMinute(10)
                    ->by('revision-access-pair:'.$key($nim, $code, $request->ip()))
                    ->response($response('Terlalu banyak percobaan verifikasi revisi. Silakan coba lagi sebentar.')),
                Limit::perMinute(60)
                    ->by('revision-access-ip:'.$key($request->ip()))
                    ->response($response('Terlalu banyak percobaan verifikasi revisi. Silakan coba lagi sebentar.')),
            ];
        });

        RateLimiter::for('revision-submit', function (Request $request) use ($key, $response): array {
            $code = (string) $request->route('kode_pengajuan', 'anonymous');

            return [
                Limit::perHour(10)
                    ->by('revision-submit-code:'.$key($code, $request->ip()))
                    ->response($response('Terlalu banyak percobaan pengiriman revisi. Silakan coba lagi nanti.')),
                Limit::perHour(60)
                    ->by('revision-submit-ip:'.$key($request->ip()))
                    ->response($response('Terlalu banyak percobaan pengiriman revisi. Silakan coba lagi nanti.')),
            ];
        });
    }
}
