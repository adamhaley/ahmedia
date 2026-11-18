<?php

namespace Tests\Http\Controllers;

use App\Enums\PageTemplate;
use App\Enums\ProjectType;
use App\Http\Controllers\PageController;
use App\Models\CaseStudy;
use App\Models\Credential;
use App\Models\Package;
use App\Models\Page;
use App\Models\Post;
use App\Models\Project;
use App\Models\ProjectImage;
use App\Models\SiteSetting;
use App\Models\Testimonial;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[CoversClass(PageController::class)]
class PageControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_keeps_the_services_body_off_the_home_hero_but_on_the_services_page(): void
    {
        $body = 'Each engagement starts with a real bottleneck, not a technology mandate.';
        $services = Page::query()->create(['title' => 'Services', 'slug' => 'services', 'template' => PageTemplate::ServicesIndex, 'is_published' => true, 'body' => $body]);

        $home = $this->get($this->homePage()->url());
        $index = $this->get($services->url());

        $home->assertOk()->assertDontSee($body, false)->assertDontSee('hero-stance', false);
        $index->assertOk()->assertSee($body, false);
    }

    #[Test]
    public function it_shows_the_hero_points_under_the_button_with_links(): void
    {
        $home = $this->homePage(['hero_points' => ['Five-star client reviews', 'Demo receptionist: [(765) 307-5537](tel:+17653075537)']]);

        $response = $this->get($home->url());

        $response->assertOk()->assertSeeInOrder(['proof-line', 'Five-star client reviews', '<a href="tel:+17653075537">(765) 307-5537</a>'], false);
    }

    #[Test]
    public function it_escapes_html_in_hero_points(): void
    {
        $home = $this->homePage(['hero_points' => ['<script>alert(1)</script>']]);

        $response = $this->get($home->url());

        $response->assertOk()->assertDontSee('<script>alert(1)</script>', false);
    }

    #[Test]
    public function it_leaves_out_the_hero_points_when_there_are_none(): void
    {
        $response = $this->get($this->homePage()->url());

        $response->assertOk()->assertDontSee('proof-line', false);
    }

    #[Test]
    public function it_shows_the_approach_steps_from_the_cms(): void
    {
        $home = $this->homePage(['section_heading' => 'Intelligence on tap', 'approach_steps' => ['Listen first', 'Then build']]);

        $response = $this->get($home->url());

        $response->assertOk()->assertSeeInOrder(['approach-steps', 'Listen first', 'Then build']);
    }

    #[Test]
    public function it_falls_back_to_the_default_approach_steps(): void
    {
        $home = $this->homePage(['section_heading' => 'Intelligence on tap']);

        $response = $this->get($home->url());

        $response->assertOk()->assertSeeInOrder(Page::DefaultApproachSteps);
    }

    #[Test]
    public function it_shows_a_service_pages_side_image_in_an_aside(): void
    {
        $page = $this->servicePage(['section_heading' => 'Automation', 'hero_heading' => 'Automate it', 'hero_image' => 'pages/side.webp']);

        $response = $this->get($page->url());

        $response->assertOk()
            ->assertSee('<aside class="split-image">', false)
            ->assertSee('src="'.Storage::disk('public')->url('pages/side.webp').'"', false)
            ->assertSee('alt="Automate it"', false);
    }

    #[Test]
    public function it_renders_no_side_image_aside_without_a_hero_image(): void
    {
        $response = $this->get($this->servicePage()->url());

        $response->assertOk()->assertDontSee('split-image', false);
    }

    #[Test]
    public function it_renders_published_packages_on_a_service_page(): void
    {
        $page = $this->servicePage();
        Package::factory()->for($page)->create([
            'title' => 'Hacked Site Recovery',
            'price' => 1000,
            'add_ons' => [['title' => 'Clean rebuild on fresh hosting', 'price' => 750, 'days' => 3]],
            'sample_document' => 'packages/recovery-report-sample.pdf',
        ]);

        $response = $this->get($page->url());

        $response->assertOk()
            ->assertSee('Hacked Site Recovery')
            ->assertSee('$1,000')
            ->assertSee('Clean rebuild on fresh hosting')
            ->assertSee('+$750')
            ->assertSee('packages/recovery-report-sample.pdf');
    }

    #[Test]
    public function it_hides_unpublished_packages(): void
    {
        $page = $this->servicePage();
        Package::factory()->for($page)->unpublished()->create(['title' => 'Secret Package']);

        $response = $this->get($page->url());

        $response->assertOk()->assertDontSee('Secret Package');
    }

    #[Test]
    public function it_still_renders_a_bullet_only_service_without_packages(): void
    {
        $page = $this->servicePage(['section_heading' => 'Pricing', 'bullets' => ['$95/hr à la carte']]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSee('$95/hr à la carte')->assertDontSee('id="packages"', false);
    }

    #[Test]
    public function it_links_case_study_cards_to_their_detail_pages(): void
    {
        $index = Page::query()->create(['title' => 'Case Studies', 'slug' => 'case-studies', 'template' => PageTemplate::CaseStudiesIndex, 'is_published' => true]);
        $caseStudy = CaseStudy::factory()->create(['narrative' => 'The full story.']);

        $response = $this->get($index->url());

        $response->assertOk()->assertSee($caseStudy->url())->assertSee('Read the case study');
    }

    #[Test]
    public function it_shows_published_case_studies_on_the_homepage(): void
    {
        $home = $this->homePage();
        Page::query()->create(['title' => 'Case Studies', 'slug' => 'case-studies', 'template' => PageTemplate::CaseStudiesIndex, 'is_published' => true, 'hero_heading' => 'Real work, in detail']);
        $caseStudy = CaseStudy::factory()->create(['title' => 'Monk Magazine', 'narrative' => 'The story.']);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('Real work, in detail')->assertSee('Monk Magazine')->assertSee($caseStudy->url());
    }

    #[Test]
    public function it_hides_the_homepage_case_studies_section_when_none_are_published(): void
    {
        $home = $this->homePage();
        Page::query()->create(['title' => 'Case Studies', 'slug' => 'case-studies', 'template' => PageTemplate::CaseStudiesIndex, 'is_published' => true]);
        CaseStudy::factory()->create(['is_published' => false]);

        $response = $this->get($home->url());

        $response->assertOk()->assertDontSee('id="case-studies"', false);
    }

    #[Test]
    public function it_takes_the_homepage_contact_copy_from_the_contact_page(): void
    {
        $home = $this->homePage();
        Page::query()->create(['title' => 'Contact', 'slug' => 'contact', 'template' => PageTemplate::Contact, 'is_published' => true, 'hero_heading' => 'Tell us what you need', 'hero_subheading' => 'We reply within a day.']);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('Tell us what you need')->assertSee('We reply within a day.');
    }

    #[Test]
    public function it_renders_the_mobile_menu_button_controlling_the_primary_nav(): void
    {
        $home = $this->homePage();

        $response = $this->get($home->url());

        $response->assertOk()
            ->assertSee('<button type="button" class="nav-toggle" aria-expanded="false" aria-controls="site-nav">', false)
            ->assertSee('<nav class="site-nav" id="site-nav"', false);
    }

    #[Test]
    public function it_renders_an_expanded_toggle_for_a_nav_items_child_pages(): void
    {
        $home = $this->homePage();
        $parent = $this->aboutPage(['show_in_nav' => true]);
        $this->servicePage(['parent_id' => $parent->id]);

        $response = $this->get($home->url());

        $response->assertOk()
            ->assertSee('class="nav-subtoggle" aria-expanded="true" aria-controls="nav-sub-0"', false)
            ->assertSee('<ul class="nav-dropdown" id="nav-sub-0">', false);
    }

    #[Test]
    public function it_takes_the_homepage_hero_from_the_home_page(): void
    {
        $home = $this->homePage(['hero_subheading' => 'Editable from the CMS.']);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('Editable from the CMS.');
    }

    #[Test]
    public function it_shows_active_testimonials_with_their_star_rating_on_the_homepage(): void
    {
        $home = $this->homePage();
        Testimonial::factory()->create(['body' => 'Lightning-fast and skilled.', 'rating' => 4]);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('Lightning-fast and skilled.')->assertSee('Rated 4 out of 5');
    }

    #[Test]
    public function it_hides_inactive_testimonials(): void
    {
        $home = $this->homePage();
        Testimonial::factory()->inactive()->create(['body' => 'Switched off.']);

        $response = $this->get($home->url());

        $response->assertOk()->assertDontSee('Switched off.')->assertDontSee('id="testimonials"', false);
    }

    #[Test]
    public function it_leaves_out_the_stars_when_a_testimonial_has_no_rating(): void
    {
        $home = $this->homePage();
        Testimonial::factory()->create(['body' => 'An endorsement.', 'rating' => null]);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('An endorsement.')->assertDontSee('testimonial-stars', false);
    }

    #[Test]
    public function it_renders_the_doppler_rings_lab_on_the_logo_page(): void
    {
        $page = Page::query()->create(['title' => 'Our Logo', 'slug' => 'logo', 'template' => PageTemplate::Logo, 'is_published' => true]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSee('data-doppler-lab', false);
    }

    #[Test]
    public function it_opens_the_logo_page_with_the_lab_instead_of_a_hero(): void
    {
        $page = Page::query()->create(['title' => 'Our Logo', 'slug' => 'logo', 'template' => PageTemplate::Logo, 'is_published' => true]);

        $response = $this->get($page->url());

        $response->assertOk()->assertDontSee('hero-section', false);
    }

    #[Test]
    public function it_heads_the_logo_page_lab_with_the_pages_hero_fields(): void
    {
        $page = Page::query()->create(['title' => 'Our Logo', 'slug' => 'logo', 'template' => PageTemplate::Logo, 'is_published' => true, 'section_eyebrow' => 'The mark', 'hero_heading' => 'Doppler rings', 'hero_subheading' => 'Where the mark came from.']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['The mark', 'Doppler rings', 'Where the mark came from.', 'doppler-lab-stage'], false);
    }

    #[Test]
    public function it_tells_the_logo_story_from_the_body_after_the_lab(): void
    {
        $page = Page::query()->create(['title' => 'Our Logo', 'slug' => 'logo', 'template' => PageTemplate::Logo, 'is_published' => true, 'body' => "First paragraph.\n\nSecond paragraph."]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['data-doppler-lab', '<p class="section-copy">First paragraph.</p>', '<p class="section-copy">Second paragraph.</p>'], false);
    }

    #[Test]
    public function it_opens_the_logo_lab_on_a_shared_arrangement(): void
    {
        $page = Page::query()->create(['title' => 'Our Logo', 'slug' => 'logo', 'template' => PageTemplate::Logo, 'is_published' => true]);

        $response = $this->get($page->url().'?seed=42&rings=5&tempo=0.12&frozen=1&travelLeft=0');

        $response->assertOk()->assertSeeInOrder([
            'name="seed" min="1" max="999" step="1" value="42"',
            'name="tempo" min="0" max="0.3" step="0.01" value="0.12"',
            'name="frozen" checked',
            'name="travelLeft" >',
        ], false);
    }

    #[Test]
    public function it_ignores_malformed_lab_settings_in_the_address(): void
    {
        $page = Page::query()->create(['title' => 'Our Logo', 'slug' => 'logo', 'template' => PageTemplate::Logo, 'is_published' => true]);

        $response = $this->get($page->url().'?motion[]=scroll&seed[]=3&rings=banana');

        $response->assertOk()->assertSee('name="rings" min="2" max="14" step="1" value="8"', false);
    }

    #[Test]
    public function it_shows_the_brand_tagline_under_the_homepage_title(): void
    {
        SiteSetting::current()->update(['footer_tagline' => 'Use AI, Stay Human']);

        $response = $this->get($this->homePage()->url());

        $response->assertOk()->assertSee('<p class="hero-tagline">Use AI, Stay Human</p>', false);
    }

    #[Test]
    public function it_takes_the_homepage_spotlight_buttons_from_the_home_page(): void
    {
        $page = $this->homePage(['section_heading' => 'How we work', 'buttons' => [['label' => 'Meet Adam', 'url' => '/about', 'style' => 'secondary']]]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSee('href="/about">Meet Adam</a>', false)->assertDontSee('More on Our Approach');
    }

    #[Test]
    public function it_leaves_certifications_off_the_about_page(): void
    {
        Credential::factory()->create(['name' => 'N8N101 Essentials', 'issuer' => 'n8n']);

        $response = $this->get($this->aboutPage()->url());

        $response->assertOk()->assertDontSee('N8N101 Essentials');
    }

    #[Test]
    public function it_shows_featured_credentials_as_track_record_stats(): void
    {
        Credential::factory()->featured('100%')->create(['name' => 'Job Success Score', 'issuer' => 'Upwork']);

        $response = $this->get($this->aboutPage()->url());

        $response->assertOk()->assertSeeInOrder(['id="track-record"', '<p class="stat-value">100%</p>', 'Job Success Score'], false);
    }

    #[Test]
    public function it_hides_inactive_credentials(): void
    {
        Credential::factory()->featured()->inactive()->create(['name' => 'Retired Certificate']);

        $response = $this->get($this->aboutPage()->url());

        $response->assertOk()->assertDontSee('Retired Certificate');
    }

    #[Test]
    public function it_shows_a_service_pages_certifications(): void
    {
        $page = $this->servicePage();
        Credential::factory()->for($page)->create(['name' => 'N8N101 Essentials', 'issuer' => 'n8n']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['id="credentials"', 'N8N101 Essentials'], false);
    }

    #[Test]
    public function it_keeps_other_credentials_off_a_service_page(): void
    {
        $page = $this->servicePage();
        Credential::factory()->create(['name' => 'About Only Certificate']);
        Credential::factory()->for($page)->inactive()->create(['name' => 'Retired Certificate']);
        Credential::factory()->for($page)->featured()->create(['name' => 'Job Success Score']);

        $response = $this->get($page->url());

        $response->assertOk()->assertDontSee('id="credentials"', false);
    }

    #[Test]
    public function it_leads_the_certifications_with_full_certificates(): void
    {
        $page = $this->servicePage();
        Credential::factory()->for($page)->create(['name' => 'N8N101 Essentials', 'sort_order' => 1]);
        Credential::factory()->for($page)->create([
            'name' => 'n8n Foundations Pathway',
            'certificate_image' => 'credentials/pathway-certificate.webp',
            'sort_order' => 2,
        ]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['credential-card-certificate', 'credentials/pathway-certificate.webp', 'n8n Foundations Pathway', 'N8N101 Essentials'], false);
    }

    #[Test]
    public function it_places_a_section_where_its_token_sits_in_the_body(): void
    {
        $page = $this->servicePage(['section_heading' => 'Automation with n8n', 'body' => "Before the badges.\n\n[[credentials]]\n\nAfter the badges."]);
        Credential::factory()->for($page)->create(['name' => 'N8N101 Essentials']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['Automation with n8n', 'Before the badges.', 'N8N101 Essentials', 'After the badges.']);
    }

    #[Test]
    public function it_keeps_unplaced_sections_in_their_default_spot(): void
    {
        $page = $this->servicePage(['section_heading' => 'Automation with n8n', 'body' => "Intro.\n\n[[projects:workflow]]\n\nOutro."]);
        Project::factory()->for($page)->create(['type' => ProjectType::Workflow, 'title' => 'Lead Intake Workflow']);
        Project::factory()->for($page)->create(['type' => ProjectType::Website, 'title' => 'Bakery Site']);
        Credential::factory()->for($page)->create(['name' => 'N8N101 Essentials']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['Intro.', 'Lead Intake Workflow', 'Outro.', 'N8N101 Essentials', 'Bakery Site']);
    }

    #[Test]
    public function it_renders_a_placed_section_only_once(): void
    {
        $page = $this->servicePage(['section_heading' => 'Automation with n8n', 'body' => "[[credentials]]\n\nOutro."]);
        Credential::factory()->for($page)->create(['name' => 'N8N101 Essentials']);

        $response = $this->get($page->url());

        $this->assertSame(1, substr_count($response->getContent(), 'id="credentials"'));
    }

    #[Test]
    public function it_shows_the_portrait_beside_the_about_story(): void
    {
        $page = $this->aboutPage(['section_heading' => 'The story', 'circle_image' => 'pages/adam.jpg', 'circle_image_alt' => 'Adam Haley']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSee('pages/adam.jpg')->assertSee('alt="Adam Haley"', false);
    }

    #[Test]
    public function it_shows_the_contact_details_and_socials_under_the_about_portrait(): void
    {
        SiteSetting::current()->update(['contact_email' => 'adam@example.com', 'instagram_url' => 'https://instagram.com/ahmedia.ai']);
        $page = $this->aboutPage(['section_heading' => 'Adam Haley', 'body' => 'The story.']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['about-aside', 'Adam Haley', 'contact-details', 'adam@example.com', 'https://instagram.com/ahmedia.ai', 'The story.'], false);
    }

    #[Test]
    public function it_lists_published_posts_newest_first_on_the_blog_index(): void
    {
        Post::factory()->create(['title' => 'Older post', 'published_at' => now()->subWeek()]);
        Post::factory()->create(['title' => 'Newer post', 'published_at' => now()->subDay()]);
        $blog = Page::query()->create(['title' => 'Blog', 'slug' => 'blog', 'template' => PageTemplate::Blog, 'is_published' => true]);

        $response = $this->get($blog->url());

        $response->assertOk()->assertSeeInOrder(['Newer post', 'Older post']);
    }

    #[Test]
    public function it_leaves_drafts_off_the_blog_index(): void
    {
        Post::factory()->draft()->create(['title' => 'Unfinished draft']);
        $blog = Page::query()->create(['title' => 'Blog', 'slug' => 'blog', 'template' => PageTemplate::Blog, 'is_published' => true]);

        $response = $this->get($blog->url());

        $response->assertOk()->assertDontSee('Unfinished draft');
    }

    #[Test]
    public function it_shows_the_address_and_map_on_the_contact_page(): void
    {
        SiteSetting::current()->update(['address' => '5161 Lankershim Blvd, North Hollywood, CA 91601']);

        $response = $this->get($this->contactPage()->url());

        $response->assertOk()->assertSee('5161 Lankershim Blvd')->assertSee('/images/contact-map.jpg');
    }

    #[Test]
    public function it_leaves_out_the_location_when_no_address_is_set(): void
    {
        $response = $this->get($this->contactPage()->url());

        $response->assertOk()->assertDontSee('id="location"', false);
    }

    #[Test]
    public function it_offers_each_case_study_tag_as_a_filter(): void
    {
        CaseStudy::factory()->create(['tags' => ['Branding']]);
        CaseStudy::factory()->create(['tags' => ['AI Engineering']]);

        $response = $this->get($this->caseStudiesPage()->url());

        $response->assertOk()->assertSeeInOrder(['All projects', 'AI Engineering', 'Branding']);
    }

    #[Test]
    public function it_filters_case_studies_by_tag(): void
    {
        CaseStudy::factory()->create(['title' => 'A brand pass', 'tags' => ['Branding']]);
        CaseStudy::factory()->create(['title' => 'An agent build', 'tags' => ['AI Engineering']]);

        $response = $this->get($this->caseStudiesPage()->url().'?tag=Branding');

        $response->assertOk()->assertSee('A brand pass')->assertDontSee('An agent build');
    }

    #[Test]
    public function it_shows_testimonials_on_the_case_studies_page(): void
    {
        Testimonial::factory()->create(['body' => 'Adam rebuilt our site in a week.']);

        $response = $this->get($this->caseStudiesPage()->url());

        $response->assertOk()->assertSee('Adam rebuilt our site in a week.');
    }

    #[Test]
    public function it_shows_a_service_pages_projects_under_their_type(): void
    {
        $page = $this->servicePage();
        Project::factory()->for($page)->create([
            'type' => ProjectType::Workflow,
            'title' => 'Lead Intake Workflow',
            'download_file' => 'projects/downloads/lead-intake.json',
            'links' => [['label' => 'Buy on Gumroad', 'url' => 'https://gumroad.com/l/lead-intake']],
            'tags' => ['n8n'],
        ]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['Workflows', 'Lead Intake Workflow', 'n8n', 'lead-intake.json', 'Buy on Gumroad']);
    }

    #[Test]
    public function it_shows_the_cover_and_gallery_in_order_inside_a_carousel(): void
    {
        $page = $this->servicePage();
        $project = Project::factory()->for($page)->create(['cover_image' => 'projects/cover.jpg']);
        ProjectImage::factory()->for($project)->create(['image' => 'projects/canvas.png']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['data-carousel', 'projects/cover.jpg', 'projects/canvas.png']);
    }

    #[Test]
    public function it_renders_carousel_controls_and_dots_for_multiple_slides(): void
    {
        $page = $this->servicePage();
        $project = Project::factory()->for($page)->create(['cover_image' => 'projects/cover.jpg']);
        ProjectImage::factory()->for($project)->create(['image' => 'projects/canvas.png']);

        $response = $this->get($page->url());

        $response->assertOk()
            ->assertSeeInOrder(['Previous image', 'Next image', 'Show image 1 of 2', 'Show image 2 of 2'])
            ->assertDontSee('2 images')
            ->assertSee('aria-roledescription="carousel"', false);
    }

    #[Test]
    public function it_marks_the_first_dot_as_current(): void
    {
        $page = $this->servicePage();
        $project = Project::factory()->for($page)->create(['cover_image' => 'projects/cover.jpg']);
        ProjectImage::factory()->for($project)->create(['image' => 'projects/canvas.png']);

        $response = $this->get($page->url());

        $response->assertSee('aria-label="Show image 1 of 2" aria-current="true"', false);
    }

    #[Test]
    public function it_renders_no_carousel_for_a_cover_only_project(): void
    {
        $page = $this->servicePage();
        Project::factory()->for($page)->create(['cover_image' => 'projects/cover.jpg']);

        $response = $this->get($page->url());

        $response->assertOk()
            ->assertSee('projects/cover.jpg')
            ->assertDontSee('data-carousel', false)
            ->assertDontSee('Previous image')
            ->assertDontSee('Show image');
    }

    #[Test]
    public function it_hides_draft_projects(): void
    {
        $page = $this->servicePage();
        Project::factory()->for($page)->draft()->create(['title' => 'Unfinished Workflow']);

        $response = $this->get($page->url());

        $response->assertOk()->assertDontSee('Unfinished Workflow');
    }

    #[Test]
    public function it_links_a_project_to_its_published_case_study(): void
    {
        $page = $this->servicePage();
        $caseStudy = CaseStudy::factory()->create(['slug' => 'lead-intake-rebuild', 'is_published' => true]);
        Project::factory()->for($page)->for($caseStudy)->create();

        $response = $this->get($page->url());

        $response->assertOk()->assertSee('/case-studies/lead-intake-rebuild');
    }

    #[Test]
    public function it_links_each_homepage_service_row_by_its_name(): void
    {
        $home = $this->homePage();
        $services = Page::query()->create(['title' => 'Services', 'slug' => 'services', 'template' => PageTemplate::ServicesIndex, 'is_published' => true]);
        $service = $this->servicePage(['parent_id' => $services->id]);

        $response = $this->get($home->url());

        $response->assertOk()->assertSee('<h3 class="service-row-name"><a href="'.$service->url().'">'.e($service->title).'</a></h3>', false);
    }

    #[Test]
    public function it_links_each_services_index_row_by_its_name(): void
    {
        $services = Page::query()->create(['title' => 'Services', 'slug' => 'services', 'template' => PageTemplate::ServicesIndex, 'is_published' => true]);
        $service = $this->servicePage(['parent_id' => $services->id]);

        $response = $this->get($services->url());

        $response->assertOk()->assertSee('<h3 class="service-row-name"><a href="'.$service->url().'">'.e($service->title).'</a></h3>', false);
    }

    #[Test]
    public function it_keeps_blog_cards_unlinked(): void
    {
        Post::factory()->create(['title' => 'A post', 'published_at' => now()->subDay()]);
        $blog = Page::query()->create(['title' => 'Blog', 'slug' => 'blog', 'template' => PageTemplate::Blog, 'is_published' => true]);

        $response = $this->get($blog->url());

        $response->assertOk()->assertDontSee('feature-card-linked', false);
    }

    #[Test]
    public function it_marks_the_current_page_in_the_nav(): void
    {
        $faq = Page::query()->create(['title' => 'FAQ', 'slug' => 'faq', 'template' => PageTemplate::Faq, 'is_published' => true, 'show_in_nav' => true]);

        $response = $this->get($faq->url());

        $response->assertOk()->assertSee('<a href="'.$faq->url().'"  aria-current="page" >FAQ</a>', false);
    }

    #[Test]
    public function it_marks_the_services_nav_item_on_a_service_page(): void
    {
        $services = Page::query()->create(['title' => 'Services', 'slug' => 'services', 'template' => PageTemplate::ServicesIndex, 'is_published' => true, 'show_in_nav' => true]);
        $service = $this->servicePage(['parent_id' => $services->id]);

        $response = $this->get($service->url());

        $response->assertOk()->assertSee('nav-item has-dropdown is-current-section', false);
    }

    #[Test]
    public function it_sets_upwork_offers_apart_from_the_package_ladder(): void
    {
        $page = $this->servicePage();
        Package::factory()->for($page)->create(['title' => 'Ladder Package', 'upwork_url' => null]);
        Package::factory()->for($page)->create(['title' => 'Upwork Offer', 'upwork_url' => 'https://www.upwork.com/services/product/1']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['package-ladder', 'Ladder Package', 'package-standalone', 'Upwork Offer'], false);
    }

    #[Test]
    public function it_leads_with_direct_contact_and_offers_upwork_second(): void
    {
        $page = $this->servicePage();
        Package::factory()->for($page)->create(['upwork_url' => 'https://www.upwork.com/services/product/1']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['button button-primary" href="/#contact">Start a Project', 'button button-secondary" href="https://www.upwork.com/services/product/1"'], false);
    }

    #[Test]
    public function it_puts_the_contact_details_beside_the_contact_form(): void
    {
        SiteSetting::current()->update(['contact_email' => 'adam@example.com', 'address' => '5161 Lankershim Blvd, North Hollywood, CA 91601']);

        $response = $this->get($this->contactPage()->url());

        $response->assertOk()->assertSeeInOrder(['contact-layout', '<form', 'contact-details', 'adam@example.com', '5161 Lankershim Blvd'], false);
    }

    #[Test]
    public function it_features_the_longest_testimonial_first(): void
    {
        Testimonial::factory()->create(['body' => 'Short.']);
        Testimonial::factory()->create(['body' => 'A much longer endorsement that should be the featured quote.']);

        $response = $this->get($this->homePage()->url());

        $response->assertOk()->assertSeeInOrder(['is-featured', 'A much longer endorsement', 'Short.'], false);
    }

    #[Test]
    public function it_shows_the_pages_own_call_to_action_line(): void
    {
        $page = $this->servicePage(['cta_heading' => 'Where are you losing calls?', 'cta_copy' => 'Hear it for yourself first.']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSee('Hear it for yourself first.');
    }

    #[Test]
    public function it_falls_back_to_the_default_call_to_action_line(): void
    {
        $page = $this->servicePage(['cta_heading' => 'Where are you losing calls?']);

        $response = $this->get($page->url());

        $response->assertOk()->assertSee(Page::DefaultCtaCopy);
    }

    #[Test]
    public function it_offers_start_a_project_in_every_hero_by_default(): void
    {
        $response = $this->get($this->aboutPage()->url());

        $response->assertOk()->assertSeeInOrder(['hero-section', 'hero-back', 'Start a Project'], false);
    }

    #[Test]
    public function it_keeps_the_contact_hero_free_of_buttons(): void
    {
        $response = $this->get($this->contactPage()->url());

        $response->assertOk()->assertDontSee('hero-back', false);
    }

    #[Test]
    public function it_shows_the_chosen_quote_beside_the_call_to_action(): void
    {
        $testimonial = Testimonial::factory()->create(['body' => 'Worked first time.', 'name' => 'Ronnie A.', 'project' => 'n8n workflow automation']);
        $page = $this->servicePage(['cta_heading' => 'Ready?', 'cta_testimonial_id' => $testimonial->id]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['cta-proof', 'Worked first time.', 'Ronnie A.', 'n8n workflow automation'], false);
    }

    #[Test]
    public function it_hides_the_quote_when_its_testimonial_is_turned_off(): void
    {
        $testimonial = Testimonial::factory()->inactive()->create(['body' => 'Switched off.']);
        $page = $this->servicePage(['cta_heading' => 'Ready?', 'cta_testimonial_id' => $testimonial->id]);

        $response = $this->get($page->url());

        $response->assertOk()->assertDontSee('Switched off.');
    }

    #[Test]
    public function it_shows_the_side_card_beside_the_service_body(): void
    {
        $page = $this->servicePage([
            'section_heading' => 'Fewer missed calls',
            'body' => 'Your AI receptionist answers on a real phone number.',
            'side_card' => ['title' => 'Hear it for yourself', 'link_label' => '(765) 307-5537', 'link_url' => 'tel:+17653075537', 'note' => 'A demo receptionist.'],
        ]);

        $response = $this->get($page->url());

        $response->assertOk()->assertSeeInOrder(['split-aside', 'Hear it for yourself', 'href="tel:+17653075537"', 'A demo receptionist.'], false);
    }

    #[Test]
    public function it_leaves_out_a_side_card_without_a_title(): void
    {
        $page = $this->servicePage([
            'section_heading' => 'Fewer missed calls',
            'body' => 'Your AI receptionist answers on a real phone number.',
            'side_card' => ['note' => 'A demo receptionist.'],
        ]);

        $response = $this->get($page->url());

        $response->assertOk()->assertDontSee('demo-card', false);
    }

    private function caseStudiesPage(): Page
    {
        return Page::query()->create(['title' => 'Case Studies', 'slug' => 'case-studies', 'template' => PageTemplate::CaseStudiesIndex, 'is_published' => true]);
    }

    private function contactPage(): Page
    {
        return Page::query()->create(['title' => 'Contact', 'slug' => 'contact', 'template' => PageTemplate::Contact, 'is_published' => true]);
    }

    /** @param array<string, mixed> $attributes */
    private function aboutPage(array $attributes = []): Page
    {
        return Page::query()->create(['title' => 'About', 'slug' => 'about', 'template' => PageTemplate::About, 'is_published' => true, ...$attributes]);
    }

    /** @param array<string, mixed> $attributes */
    private function homePage(array $attributes = []): Page
    {
        return Page::query()->create(['title' => 'AH Media', 'template' => PageTemplate::Home, 'is_published' => true, ...$attributes]);
    }

    /** @param array<string, mixed> $attributes */
    private function servicePage(array $attributes = []): Page
    {
        return Page::query()->create([
            'title' => 'Security & Launch',
            'slug' => 'security-and-launch',
            'template' => PageTemplate::Service,
            'is_published' => true,
            ...$attributes,
        ]);
    }
}
