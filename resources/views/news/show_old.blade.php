@extends('layouts.app')

@section('title', $news->title)

@section('content')
<div class="min-vh-100" style="background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);">
    <!-- Navigation Bar -->
    <nav class="py-3" style="background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(0,0,0,0.05);">
        <div class="container">
            <ol class="breadcrumb mb-0 d-flex align-items-center">
                <li class="breadcrumb-item">
                    <a href="{{ url('/') }}" class="text-decoration-none text-muted d-flex align-items-center">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" class="me-1">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                        </svg>
                        Trang chủ
                    </a>
                </li>
                <li class="breadcrumb-item">
                    <a href="{{ route('articles.index') }}" class="text-decoration-none" style="color: #6366f1;">Tin tức</a>
                </li>
                <li class="breadcrumb-item active text-secondary">{{ Str::limit($news->title, 40) }}</li>
            </ol>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container py-5">
        <div class="row g-5">
            <!-- Article Content -->
            <div class="col-lg-8">
                <!-- Article Card -->
                <article class="bg-white shadow-sm" style="border-radius: 20px; overflow: hidden;">
                    <!-- Cover Image -->
                    @if($news->cover_image)
                    <div class="position-relative" style="height: 500px; background: linear-gradient(to bottom, #e0e7ff, #f3f4f6);">
                        <img src="{{ asset('storage/' . $news->cover_image) }}"
                             alt="{{ $news->title }}"
                             class="w-100 h-100"
                             style="object-fit: cover; object-position: center;">
                        
                        <!-- Category Badge Overlay -->
                        <div class="position-absolute top-0 end-0 m-4">
                            @php
                                $categoryStyles = [
                                    'NEWS' => ['bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'icon' => '<path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/><path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>'],
                                    'ANNOUNCEMENT' => ['bg' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)', 'icon' => '<path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>'],
                                    'EVENT' => ['bg' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)', 'icon' => '<path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>'],
                                    'GUIDE' => ['bg' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)', 'icon' => '<path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>']
                                ];
                                $style = $categoryStyles[$news->category] ?? ['bg' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)', 'icon' => ''];
                            @endphp
                            <span class="badge text-white px-4 py-2 d-flex align-items-center shadow-lg" 
                                  style="background: {{ $style['bg'] }}; border-radius: 50px; font-size: 0.875rem; font-weight: 600;">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" class="me-2">
                                    {!! $style['icon'] !!}
                                </svg>
                                {{ $news->category_name }}
                                'NEWS' => 'primary',
                                'ANNOUNCEMENT' => 'danger',
                                'EVENT' => 'success',
                                'GUIDE' => 'info'
                            ];
                            $bgColor = $categoryColors[$news->category] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $bgColor }} px-3 py-2 fs-6">{{ $news->category_name }}</span>
                        @if($news->is_featured)
                            <span class="badge bg-warning text-dark px-3 py-2 fs-6">
                                <i class="fas fa-star"></i> Nổi bật
                            </span>
                        </div>

                        @if($news->is_featured)
                        <div class="position-absolute top-0 start-0 m-4">
                            <span class="badge text-dark px-4 py-2 d-flex align-items-center shadow-lg" 
                                  style="background: linear-gradient(135deg, #ffd89b 0%, #ffba69 100%); border-radius: 50px; font-size: 0.875rem; font-weight: 600;">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" class="me-2">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                                Nổi bật
                            </span>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Article Body -->
                    <div class="p-5">
                        <!-- Title -->
                        <h1 class="fw-bold mb-4" style="font-size: 2.5rem; line-height: 1.3; color: #1a202c; letter-spacing: -0.02em;">
                            {{ $news->title }}
                        </h1>

                        <!-- Meta Information -->
                        <div class="d-flex flex-wrap align-items-center gap-4 pb-4 mb-5 border-bottom">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-2" 
                                     style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <svg width="20" height="20" fill="white" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div>
                                    <div class="fw-semibold" style="font-size: 0.875rem; color: #1a202c;">
                                        {{ $news->createdBy->full_name ?? 'Admin' }}
                                    </div>
                                    <div class="text-muted" style="font-size: 0.75rem;">Tác giả</div>
                                </div>
                            </div>

                            <div class="d-flex align-items-center text-muted">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" class="me-2">
                                    <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                </svg>
                                <span style="font-size: 0.875rem;">{{ $news->published_at->format('d/m/Y H:i') }}</span>
                            </div>

                            @if($news->conference)
                            <div class="d-flex align-items-center">
                                <svg width="18" height="18" fill="currentColor" viewBox="0 0 20 20" class="me-2" style="color: #6366f1;">
                                    <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/>
                                </svg>
                                <a href="{{ route('articles.index', ['conference_id' => $news->conference_id]) }}"
                                   class="text-decoration-none fw-semibold" style="color: #6366f1; font-size: 0.875rem;">
                                    {{ $news->conference->title }}
                                </a>
                            </div>
                            @endif
                        </div>

                        <!-- Summary -->
                        @if($news->summary)
                        <div class="position-relative p-4 mb-5" style="background: linear-gradient(135deg, #e0e7ff 0%, #f0f9ff 100%); border-radius: 16px; border-left: 4px solid #6366f1;">
                            <div class="d-flex align-items-start">
                                <div class="flex-shrink-0 me-3">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 48px; height: 48px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                        <svg width="24" height="24" fill="white" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-2" style="color: #4338ca; font-size: 1rem;">Tóm tắt nội dung</h6>
                                    <p class="mb-0" style="color: #4338ca; line-height: 1.7; font-size: 1rem;">{{ $news->summary }}</p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Article Content -->
                        <div class="article-content" style="font-size: 1.125rem; line-height: 1.9; color: #374151;">
                            {!! nl2br(e($news->content)) !!}
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex justify-content-between align-items-center mt-5 pt-5 border-top">
                            <a href="{{ route('articles.index') }}" 
                               class="btn btn-lg px-4 py-3 d-flex align-items-center"
                               style="background: white; border: 2px solid #e5e7eb; color: #374151; border-radius: 12px; font-weight: 600; transition: all 0.3s;">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 20 20" class="me-2">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                </svg>
                                Quay lại
                            </a>

                            <div class="d-flex gap-2 align-items-center">
                                <span class="text-muted fw-semibold me-2" style="font-size: 0.875rem;">Chia sẻ:</span>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.show', $news->slug)) }}"
                                   target="_blank"
                                   class="btn d-flex align-items-center justify-content-center"
                                   style="width: 48px; height: 48px; background: linear-gradient(135deg, #1877f2 0%, #0d5ddb 100%); border: none; border-radius: 12px; color: white;">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                    </svg>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('articles.show', $news->slug)) }}&text={{ urlencode($news->title) }}"
                                   target="_blank"
                                   class="btn d-flex align-items-center justify-content-center"
                                   style="width: 48px; height: 48px; background: linear-gradient(135deg, #1da1f2 0%, #0d8bd9 100%); border: none; border-radius: 12px; color: white;">
                                    <svg width="20" height="20" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </article>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Related Articles -->
                @if($relatedNews->count() > 0)
                <div class="bg-white shadow-sm mb-4" style="border-radius: 20px; overflow: hidden;">
                            <div class="d-flex">
                                <div class="flex-shrink-0">
                                    <svg width="24" height="24" fill="#3b82f6" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="fw-bold mb-2" style="color: #1e40af;">Tóm tắt nội dung</h6>
                                    <p class="mb-0" style="color: #1e3a8a; line-height: 1.7;">{{ $news->summary }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="news-content" style="font-size: 1.125rem; line-height: 1.9; color: #374151;">
                        {!! nl2br(e($news->content)) !!}
                    </div>

                    <!-- Actions -->
                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <a href="{{ route('articles.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                <svg class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd"/>
                                </svg>
                                Quay lại danh sách
                            </a>
                            <div class="d-flex gap-2">
                                <span class="text-muted me-2 align-self-center">Chia sẻ:</span>
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.show', $news->slug)) }}"
                                   target="_blank" class="btn btn-primary btn-lg" title="Chia sẻ Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('articles.show', $news->slug)) }}&text={{ urlencode($news->title) }}"
                                   target="_blank" class="btn btn-info btn-lg text-white" title="Chia sẻ Twitter">
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
            <div class="card shadow-sm border-0 mb-4" style="border-radius: 1rem;">
                <div class="card-header text-white border-0 p-4" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%); border-radius: 1rem 1rem 0 0;">
                    <h5 class="mb-0 fw-bold">
                        <svg class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                            <path d="M15 7h1a2 2 0 012 2v5.5a1.5 1.5 0 01-3 0V7z"/>
                        </svg>
                        Tin liên quan
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    @foreach($relatedNews as $related)
                        <a href="{{ route('articles.show', $related->slug) }}"
                           class="list-group-item list-group-item-action border-0 p-3 hover-shadow">
                            <div class="d-flex gap-3">
                                @if($related->cover_image)
                                    <img src="{{ asset('storage/' . $related->cover_image) }}"
                                         alt="{{ $related->title }}"
                                         class="rounded shadow-sm"
                                         style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm"
                                         style="width: 80px; height: 80px;">
                                        <svg width="32" height="32" fill="#9ca3af" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                @endif

                                <div class="flex-grow-1">
                                    <h6 class="mb-2 fw-semibold" style="line-height: 1.4;">{{ Str::limit($related->title, 60) }}</h6>
                                    <small class="text-muted">
                                        <svg class="me-1" width="14" height="14" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                                        </svg>
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
            <div class="card shadow-sm border-0" style="border-radius: 1rem;">
                <div class="card-header text-white border-0 p-4" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%); border-radius: 1rem 1rem 0 0;">
                    <h5 class="mb-0 fw-bold">
                        <svg class="me-2" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.293.707l-2 2A1 1 0 018 17v-5.586L3.293 6.707A1 1 0 013 6V3z" clip-rule="evenodd"/>
                        </svg>
                        Danh mục
                    </h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('articles.index', ['category' => 'NEWS']) }}"
                       class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center">
                        <svg class="me-3 text-primary" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h8a2 2 0 012 2v10a2 2 0 002 2H4a2 2 0 01-2-2V5zm3 1h6v4H5V6zm6 6H5v2h6v-2z" clip-rule="evenodd"/>
                        </svg>
                        <span class="fw-medium">Tin tức</span>
                    </a>
                    <a href="{{ route('articles.index', ['category' => 'ANNOUNCEMENT']) }}"
                       class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center">
                        <svg class="me-3 text-danger" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"/>
                        </svg>
                        <span class="fw-medium">Thông báo</span>
                    </a>
                    <a href="{{ route('articles.index', ['category' => 'EVENT']) }}"
                       class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center">
                        <svg class="me-3 text-success" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/>
                        </svg>
                        <span class="fw-medium">Sự kiện</span>
                    </a>
                    <a href="{{ route('articles.index', ['category' => 'GUIDE']) }}"
                       class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center">
                        <svg class="me-3 text-info" width="20" height="20" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"/>
                        </svg>
                        <span class="fw-medium">Hướng dẫn</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1) !important;
    transform: translateX(5px);
}
.news-content p {
    margin-bottom: 1.5rem;
}
.card {
    transition: all 0.3s ease;
}
</style>
@endpush
@endsection
