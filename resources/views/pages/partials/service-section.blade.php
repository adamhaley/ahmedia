{{-- One section of a service page, by token name (Page::SectionToken). `projects` renders every
     project type the body doesn't place on its own with `projects:<type>`. --}}
@if ($section === 'credentials' && $credentials->isNotEmpty())
<x-credential-grid :credentials="$credentials" />
@elseif ($section === 'packages' && $packages->isNotEmpty())
<x-package-grid :packages="$packages" />
@elseif ($section === 'projects' || str_starts_with($section, 'projects:'))
@foreach ($projectGroups as $type => $projects)
@if ($section === "projects:{$type}" || ($section === 'projects' && ! $placed->contains("projects:{$type}")))
<x-project-grid :type="\App\Enums\ProjectType::from($type)" :projects="$projects" />
@endif
@endforeach
@endif
