<?php

namespace Tests\Views\Components;

use App\Enums\PageTemplate;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversNothing]
class CtaBandTest extends TestCase
{
    use RefreshDatabase;

    private function servicePage(): Page
    {
        return Page::query()->create([
            'title' => 'AI Consulting',
            'slug' => 'ai-consulting',
            'template' => PageTemplate::Service,
            'is_published' => true,
            'cta_heading' => "Let's talk about your project",
        ]);
    }

    #[Test]
    public function it_renders_a_hidden_contact_form_under_a_start_a_project_cta(): void
    {
        $response = $this->get($this->servicePage()->url());

        $response->assertOk()->assertSee('<div id="cta-contact-form" class="cta-contact-form"  hidden >', false);
    }

    #[Test]
    public function it_opens_the_form_after_a_submission(): void
    {
        $response = $this->withSession(['contact_status' => 'Thanks!'])->get($this->servicePage()->url());

        $response->assertOk()->assertSee('Thanks!')->assertDontSee('class="cta-contact-form"  hidden', false);
    }

    #[Test]
    public function it_does_not_add_a_form_when_the_cta_points_elsewhere(): void
    {
        $html = (string) $this->blade('<x-cta-band heading="See the package" buttonHref="/services/website-design/#brand-audit" />');

        $this->assertStringNotContainsString('cta-contact-form', $html);
    }
}
