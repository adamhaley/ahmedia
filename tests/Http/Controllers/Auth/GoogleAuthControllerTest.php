<?php

namespace Tests\Http\Controllers\Auth;

use App\Http\Controllers\Auth\GoogleAuthController;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(GoogleAuthController::class)]
class GoogleAuthControllerTest extends TestCase
{
    #[Test]
    public function it_redirects_to_google_with_a_callback_on_the_current_host(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
        ]);

        $response = $this->get('https://ahmedia.ai/auth/google');

        $response->assertRedirectContains(
            'redirect_uri='.urlencode('https://ahmedia.ai/auth/google/callback')
        );
    }
}
