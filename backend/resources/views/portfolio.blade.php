@extends('layouts.app')

@section('title', 'Portofolio Proyek')

@section('content')
    <section class="section-container">
        <div class="section-title-wrap">
            <span class="section-subtitle">Galeri Karya</span>
            <h2 class="section-title">Semua <span>Proyek &amp; Aplikasi</span></h2>
        </div>
        
        <div class="grid-3">
            @forelse($projects as $project)
                <div class="card-project">
                    <div class="project-cover">
                        @if($project->image)
                            <img src="{{ asset('storage/' . $project->image) }}" alt="{{ $project->title }}">
                        @else
                            <div class="project-cover-placeholder">💻</div>
                        @endif
                    </div>
                    <div class="project-body">
                        <h3 class="project-title">{{ $project->title }}</h3>
                        <p class="project-desc">{{ $project->description }}</p>
                        
                        <div class="project-tags">
                            @foreach(explode(',', $project->technologies) as $tech)
                                <span class="tag-tech">{{ trim($tech) }}</span>
                            @endforeach
                        </div>
                        
                        @if($project->project_link)
                            <div class="project-footer">
                                <a href="{{ route('projects.visit', $project->id) }}" target="_blank" rel="noopener noreferrer" class="project-link-icon">
                                    Buka Aplikasi
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div style="text-align: center; grid-column: 1/-1; padding: 4rem 2rem;">
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Belum ada portofolio proyek yang ditambahkan.</p>
                    <a href="{{ route('home') }}" class="btn btn-secondary">Kembali ke Beranda</a>
                </div>
            @endforelse
        </div>
    </section>
@endsection
