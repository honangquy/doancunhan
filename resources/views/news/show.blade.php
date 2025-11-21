@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mb-4">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Trang chủ</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('news.index') }}">Tin tức</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($news->title, 50) }}</li>
                </ol>
            </nav>

            <!-- News Article -->
            <article class="card shadow-sm mb-4">
                @if($news->cover_image)
                    <img src="{{ asset('storage/' . $news->cover_image) }}"
                         class="card-img-top"
                         alt="{{ $news->title }}">
                @endif

                <div class="card-body">
                    <!-- Meta Info -->
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $news->category_name }}</span>
                        @if($news->is_featured)
                            <span class="badge bg-warning text-dark">
                                <i class="fas fa-star"></i> Nổi bật
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="display-6 fw-bold mb-3">{{ $news->title }}</h1>

                    <!-- Meta -->
                    <div class="text-muted mb-4 pb-3 border-bottom">
                        <i class="far fa-calendar me-2"></i>
                        {{ $news->published_at->format('d/m/Y H:i') }}

                        @if($news->conference)
                            <span class="mx-2">•</span>
                            <i class="far fa-building me-2"></i>
                            <a href="{{ route('news.index', ['conference_id' => $news->conference_id]) }}"
                               class="text-decoration-none">
                                {{ $news->conference->title }}
                            </a>
                        @endif

                        <span class="mx-2">•</span>
                        <i class="far fa-user me-2"></i>
                        {{ $news->createdBy->full_name ?? 'Admin' }}
                    </div>

                    <!-- Summary -->
                    @if($news->summary)
                        <div class="alert alert-light border-start border-primary border-4 mb-4">
                            <strong><i class="fas fa-quote-left me-2"></i>Tóm tắt:</strong>
                            <p class="mb-0 mt-2">{{ $news->summary }}</p>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="news-content">
                        {!! nl2br(e($news->content)) !!}
                    </div>

                    <!-- Tags/Actions -->
                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left me-2"></i>Quay lại danh sách
                                </a>
                            </div>
                            <div>
                                <!-- Social Share Buttons -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.show', $news->slug)) }}"
                                   target="_blank" class="btn btn-outline-primary btn-sm" title="Chia sẻ Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('articles.show', $news->slug)) }}&text={{ urlencode($news->title) }}"
                                   target="_blank" class="btn btn-outline-info btn-sm" title="Chia sẻ Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </article>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Related News -->
            @if($relatedNews->count() > 0)
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-newspaper me-2"></i>Tin liên quan
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($relatedNews as $related)
                        <a href="{{ route('articles.show', $related->slug) }}"
                           class="list-group-item list-group-item-action">
                            <div class="d-flex gap-3">
                                @if($related->cover_image)
                                    <img src="{{ asset('storage/' . $related->cover_image) }}"
                                         alt="{{ $related->title }}"
                                         class="rounded"
                                         style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                         style="width: 60px; height: 60px;">
                                        <i class="fas fa-image text-muted"></i>
                                    </div>
                                @endif

                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($related->title, 60) }}</h6>
                                    <small class="text-muted">
                                        <i class="far fa-calendar me-1"></i>
                                        {{ $related->published_at->format('d/m/Y') }}
                                    </small>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Category Filter -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-filter me-2"></i>Lọc theo danh mục
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('news.index', ['category' => 'NEWS']) }}"
                       class="list-group-item list-group-item-action">
                        Tin tức
                    </a>
                    <a href="{{ route('news.index', ['category' => 'ANNOUNCEMENT']) }}"
                       class="list-group-item list-group-item-action">
                        Thông báo
                    </a>
                    <a href="{{ route('news.index', ['category' => 'EVENT']) }}"
                       class="list-group-item list-group-item-action">
                        Sự kiện
                    </a>
                    <a href="{{ route('news.index', ['category' => 'GUIDE']) }}"
                       class="list-group-item list-group-item-action">
                        Hướng dẫn
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.news-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #333;
}
.news-content p {
    margin-bottom: 1.5rem;
}
</style>
@endpush
@endsection
