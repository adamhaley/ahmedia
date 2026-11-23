<?php

namespace Tests\Http;

use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class StyleguideRouteTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_does_not_serve_the_style_guide_outside_local_development(): void
    {
        $response = $this->get('/styleguide');

        $response->assertNotFound();
    }
}
