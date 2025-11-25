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
                    <div class="mb-4 d-flex flex-wrap gap-2">
                        @php
                            $categoryColors = [
                                'NEWS' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                                'EVENT' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)',
                                'ANNOUNCEMENT' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
                                'GUIDE' => 'linear-gradient(135deg, #43e97b 0%, #38f9d7 100%)'
                            ];
                            $categoryColor = $categoryColors[$news->category] ?? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
                        @endphp
                        <span class="modern-badge" style="background: {{ $categoryColor }}">
                            <svg width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                <path d="M2 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v13.5a.5.5 0 0 1-.777.416L8 13.101l-5.223 2.815A.5.5 0 0 1 2 15.5V2zm2-1a1 1 0 0 0-1 1v12.566l4.723-2.482a.5.5 0 0 1 .554 0L13 14.566V2a1 1 0 0 0-1-1H4z"/>
                            </svg>
                            {{ $news->category_name }}
                        </span>
                        @if($news->is_featured)
                            <span class="modern-badge" style="background: linear-gradient(135deg, #f6d365 0%, #fda085 100%)">
                                <svg width="14" height="14" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                    <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                </svg>
                                Nổi bật
                            </span>
                        @endif
                    </div>

                    <!-- Title -->
                    <h1 class="article-title mb-4">{{ $news->title }}</h1>

                    <!-- Meta -->
                    <div class="article-meta mb-4 pb-4 border-bottom">
                        <div class="d-flex flex-wrap gap-3 text-muted">
                            <div class="meta-item">
                                <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                </svg>
                                {{ $news->published_at->format('d/m/Y H:i') }}
                            </div>

                            @if($news->conference)
                                <div class="meta-item">
                                    <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                        <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5v-7zm1.294 7.456A1.999 1.999 0 0 1 4.732 11h5.536a2.01 2.01 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456zM12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12v4zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                                    </svg>
                                    <a href="{{ route('news.index', ['conference_id' => $news->conference_id]) }}"
                                       class="text-decoration-none text-primary">
                                        {{ $news->conference->title }}
                                    </a>
                                </div>
                            @endif

                            <div class="meta-item">
                                <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                    <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6Zm2-3a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4Zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10Z"/>
                                </svg>
                                {{ $news->createdBy->full_name ?? 'Admin' }}
                            </div>
                        </div>
                    </div>

                    <!-- Summary -->
                    @if($news->summary)
                        <div class="summary-box mb-4">
                            <div class="summary-icon">
                                <svg width="20" height="20" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M7.657 6.247c.11-.33.576-.33.686 0l.645 1.937a2.89 2.89 0 0 0 1.829 1.828l1.936.645c.33.11.33.576 0 .686l-1.937.645a2.89 2.89 0 0 0-1.828 1.829l-.645 1.936a.361.361 0 0 1-.686 0l-.645-1.937a2.89 2.89 0 0 0-1.828-1.828l-1.937-.645a.361.361 0 0 1 0-.686l1.937-.645a2.89 2.89 0 0 0 1.828-1.828l.645-1.937zM3.794 1.148a.217.217 0 0 1 .412 0l.387 1.162c.173.518.579.924 1.097 1.097l1.162.387a.217.217 0 0 1 0 .412l-1.162.387A1.734 1.734 0 0 0 4.593 5.69l-.387 1.162a.217.217 0 0 1-.412 0L3.407 5.69A1.734 1.734 0 0 0 2.31 4.593l-1.162-.387a.217.217 0 0 1 0-.412l1.162-.387A1.734 1.734 0 0 0 3.407 2.31l.387-1.162zM10.863.099a.145.145 0 0 1 .274 0l.258.774c.115.346.386.617.732.732l.774.258a.145.145 0 0 1 0 .274l-.774.258a1.156 1.156 0 0 0-.732.732l-.258.774a.145.145 0 0 1-.274 0l-.258-.774a1.156 1.156 0 0 0-.732-.732L9.1 2.137a.145.145 0 0 1 0-.274l.774-.258c.346-.115.617-.386.732-.732L10.863.1z"/>
                                </svg>
                            </div>
                            <div class="summary-content">
                                <strong class="summary-label">Tóm tắt nội dung</strong>
                                <p class="summary-text mb-0">{{ $news->summary }}</p>
                            </div>
                        </div>
                    @endif

                    <!-- Content -->
                    <div class="news-content">
                        {!! nl2br(e($news->content)) !!}
                    </div>

                    <!-- Attachments -->
                    @if($news->attachment_path)
                        <div class="attachment-box">
                            <div class="attachment-icon">
                                <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M14 4.5V14a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V2a2 2 0 0 1 2-2h5.5L14 4.5zm-3 0A1.5 1.5 0 0 1 9.5 3V1H4a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4.5h-2z"/>
                                    <path d="M4.603 14.087a.81.81 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.68 7.68 0 0 1 1.482-.645 19.697 19.697 0 0 0 1.062-2.227 7.269 7.269 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a10.954 10.954 0 0 0 .98 1.686 5.753 5.753 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.856.856 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.712 5.712 0 0 1-.911-.95 11.651 11.651 0 0 0-1.997.406 11.307 11.307 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.793.793 0 0 1-.58.029zm1.379-1.901c-.166.076-.32.156-.459.238-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361.01.022.02.036.026.044a.266.266 0 0 0 .035-.012c.137-.056.355-.235.635-.572a8.18 8.18 0 0 0 .45-.606zm1.64-1.33a12.71 12.71 0 0 1 1.01-.193 11.744 11.744 0 0 1-.51-.858 20.801 20.801 0 0 1-.5 1.05zm2.446.45c.15.163.296.3.435.41.24.19.407.253.498.256a.107.107 0 0 0 .07-.015.307.307 0 0 0 .094-.125.436.436 0 0 0 .059-.2.095.095 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a3.876 3.876 0 0 0-.612-.053zM8.078 7.8a6.7 6.7 0 0 0 .2-.828c.031-.188.043-.343.038-.465a.613.613 0 0 0-.032-.198.517.517 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822.024.111.054.227.09.346z"/>
                                </svg>
                            </div>
                            <div class="attachment-content">
                                <h6 class="attachment-title">Tài liệu đính kèm</h6>
                                <a href="{{ asset('storage/' . $news->attachment_path) }}" target="_blank" class="download-btn">
                                    <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                        <path d="M.5 9.9a.5.5 0 0 1 .5.5v2.5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2.5a.5.5 0 0 1 1 0v2.5a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2v-2.5a.5.5 0 0 1 .5-.5z"/>
                                        <path d="M7.646 11.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 10.293V1.5a.5.5 0 0 0-1 0v8.793L5.354 8.146a.5.5 0 1 0-.708.708l3 3z"/>
                                    </svg>
                                    Tải xuống tài liệu
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Gallery -->
                    @if(!empty($news->images))
                        <div class="mt-4">
                            <h5 class="h6 mb-3"><i class="fas fa-images me-2"></i>Thư viện ảnh</h5>
                            <div class="row g-2">
                                @foreach($news->images as $image)
                                    <div class="col-6 col-md-4">
                                        <a href="{{ asset('storage/' . $image) }}" target="_blank">
                                            <img src="{{ asset('storage/' . $image) }}" class="img-fluid rounded border" alt="Gallery Image">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Tags/Actions -->
                    <div class="mt-5 pt-4 border-top">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <a href="{{ route('articles.index') }}" class="modern-btn modern-btn-secondary">
                                    <svg width="16" height="16" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
                                    </svg>
                                    Quay lại danh sách
                                </a>
                            </div>
                            <div class="d-flex gap-2">
                                <!-- Social Share Buttons -->
                                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.show', $news->slug)) }}"
                                   target="_blank" class="social-btn social-btn-facebook" title="Chia sẻ Facebook">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                                    </svg>
                                </a>
                                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('articles.show', $news->slug)) }}&text={{ urlencode($news->title) }}"
                                   target="_blank" class="social-btn social-btn-twitter" title="Chia sẻ Twitter">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
                                    </svg>
                                </a>
                                <a href="https://www.linkedin.com/shareArticle?mini=true&url={{ urlencode(route('articles.show', $news->slug)) }}&title={{ urlencode($news->title) }}"
                                   target="_blank" class="social-btn social-btn-linkedin" title="Chia sẻ LinkedIn">
                                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
                                    </svg>
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
            <div class="related-news-card">
                <div class="related-news-header">
                    <svg width="20" height="20" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                        <path d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5v-11zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5H12z"/>
                        <path d="M2 3h10v2H2V3zm0 3h4v3H2V6zm0 4h4v1H2v-1zm0 2h4v1H2v-1zm5-6h2v1H7V6zm3 0h2v1h-2V6zM7 8h2v1H7V8zm3 0h2v1h-2V8zm-3 2h2v1H7v-1zm3 0h2v1h-2v-1zm-3 2h2v1H7v-1zm3 0h2v1h-2v-1z"/>
                    </svg>
                    <h5 class="mb-0">Tin liên quan</h5>
                </div>
                <div class="related-news-list">
                    @foreach($relatedNews as $related)
                        <a href="{{ route('articles.show', $related->slug) }}" class="related-news-item">
                            <div class="related-news-image">
                                @if($related->cover_image)
                                    <img src="{{ asset('storage/' . $related->cover_image) }}"
                                         alt="{{ $related->title }}">
                                @else
                                    <div class="related-news-placeholder">
                                        <svg width="24" height="24" fill="currentColor" viewBox="0 0 16 16">
                                            <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                            <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5 0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002 12V3a1 1 0 0 1 1-1h12z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="related-news-content">
                                <h6 class="related-news-title">{{ Str::limit($related->title, 65) }}</h6>
                                <div class="related-news-meta">
                                    <svg width="12" height="12" fill="currentColor" class="me-1" viewBox="0 0 16 16">
                                        <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                                    </svg>
                                    {{ $related->published_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Category Filter -->
            <div class="category-card">
                <div class="category-header">
                    <svg width="20" height="20" fill="currentColor" class="me-2" viewBox="0 0 16 16">
                        <path d="M6 10.5a.5.5 0 0 1 .5-.5h3a.5.5 0 0 1 0 1h-3a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h7a.5.5 0 0 1 0 1h-7a.5.5 0 0 1-.5-.5zm-2-3a.5.5 0 0 1 .5-.5h11a.5.5 0 0 1 0 1h-11a.5.5 0 0 1-.5-.5z"/>
                    </svg>
                    <h5 class="mb-0">Danh mục</h5>
                </div>
                <div class="category-list">
                    <a href="{{ route('news.index', ['category' => 'NEWS']) }}" class="category-item category-news">
                        <div class="category-icon">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5v-11zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5H12z"/>
                            </svg>
                        </div>
                        <span>Tin tức</span>
                        <svg width="16" height="16" fill="currentColor" class="arrow" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </a>
                    <a href="{{ route('news.index', ['category' => 'ANNOUNCEMENT']) }}" class="category-item category-announcement">
                        <div class="category-icon">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                            </svg>
                        </div>
                        <span>Thông báo</span>
                        <svg width="16" height="16" fill="currentColor" class="arrow" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </a>
                    <a href="{{ route('news.index', ['category' => 'EVENT']) }}" class="category-item category-event">
                        <div class="category-icon">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M3.5 0a.5.5 0 0 1 .5.5V1h8V.5a.5.5 0 0 1 1 0V1h1a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V3a2 2 0 0 1 2-2h1V.5a.5.5 0 0 1 .5-.5zM1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1z"/>
                            </svg>
                        </div>
                        <span>Sự kiện</span>
                        <svg width="16" height="16" fill="currentColor" class="arrow" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </a>
                    <a href="{{ route('news.index', ['category' => 'GUIDE']) }}" class="category-item category-guide">
                        <div class="category-icon">
                            <svg width="18" height="18" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M1 2.828c.885-.37 2.154-.769 3.388-.893 1.33-.134 2.458.063 3.112.752v9.746c-.935-.53-2.12-.603-3.213-.493-1.18.12-2.37.461-3.287.811V2.828zm7.5-.141c.654-.689 1.782-.886 3.112-.752 1.234.124 2.503.523 3.388.893v9.923c-.918-.35-2.107-.692-3.287-.81-1.094-.111-2.278-.039-3.213.492V2.687zM8 1.783C7.015.936 5.587.81 4.287.94c-1.514.153-3.042.672-3.994 1.105A.5.5 0 0 0 0 2.5v11a.5.5 0 0 0 .707.455c.882-.4 2.303-.881 3.68-1.02 1.409-.142 2.59.087 3.223.877a.5.5 0 0 0 .78 0c.633-.79 1.814-1.019 3.222-.877 1.378.139 2.8.62 3.681 1.02A.5.5 0 0 0 16 13.5v-11a.5.5 0 0 0-.293-.455c-.952-.433-2.48-.952-3.994-1.105C10.413.809 8.985.936 8 1.783z"/>
                            </svg>
                        </div>
                        <span>Hướng dẫn</span>
                        <svg width="16" height="16" fill="currentColor" class="arrow" viewBox="0 0 16 16">
                            <path fill-rule="evenodd" d="M4.646 1.646a.5.5 0 0 1 .708 0l6 6a.5.5 0 0 1 0 .708l-6 6a.5.5 0 0 1-.708-.708L10.293 8 4.646 2.354a.5.5 0 0 1 0-.708z"/>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Article Title */
.article-title {
    font-size: 2.25rem;
    font-weight: 700;
    line-height: 1.3;
    color: #1a202c;
}

/* Article Meta */
.article-meta .meta-item {
    display: inline-flex;
    align-items: center;
    font-size: 0.9rem;
}

/* Modern Badges */
.modern-badge {
    display: inline-flex;
    align-items: center;
    padding: 0.5rem 1rem;
    border-radius: 50px;
    color: white;
    font-size: 0.875rem;
    font-weight: 600;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
}

.modern-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Summary Box */
.summary-box {
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
    border-left: 4px solid #667eea;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    gap: 1rem;
}

.summary-icon {
    flex-shrink: 0;
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}

.summary-content {
    flex: 1;
}

.summary-label {
    display: block;
    color: #667eea;
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.summary-text {
    color: #4a5568;
    line-height: 1.7;
    font-size: 1rem;
}

/* News Content */
.news-content {
    font-size: 1.1rem;
    line-height: 1.8;
    color: #2d3748;
}

.news-content p {
    margin-bottom: 1.5rem;
}

/* Attachment Box */
.attachment-box {
    margin-top: 2rem;
    background: linear-gradient(135deg, #f6f8fb 0%, #ffffff 100%);
    border: 2px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    gap: 1.25rem;
    align-items: flex-start;
    transition: all 0.3s ease;
}

.attachment-box:hover {
    border-color: #667eea;
    box-shadow: 0 8px 16px rgba(102, 126, 234, 0.1);
}

.attachment-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.attachment-content {
    flex: 1;
}

.attachment-title {
    font-size: 1rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.75rem;
}

.download-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.625rem 1.25rem;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.9rem;
    transition: all 0.3s ease;
}

.download-btn:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(102, 126, 234, 0.3);
}

/* Modern Buttons */
.modern-btn {
    display: inline-flex;
    align-items: center;
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    font-size: 0.95rem;
}

.modern-btn-secondary {
    background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
    color: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modern-btn-secondary:hover {
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
}

/* Social Buttons */
.social-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    text-decoration: none;
    transition: all 0.3s ease;
    color: white;
}

.social-btn-facebook {
    background: linear-gradient(135deg, #1877f2 0%, #0c63d4 100%);
}

.social-btn-twitter {
    background: linear-gradient(135deg, #1da1f2 0%, #0c85d0 100%);
}

.social-btn-linkedin {
    background: linear-gradient(135deg, #0077b5 0%, #005885 100%);
}

.social-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    color: white;
}

/* Related News Card */
.related-news-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 1.5rem;
}

.related-news-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
}

.related-news-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.related-news-list {
    padding: 0.5rem;
}

.related-news-item {
    display: flex;
    gap: 1rem;
    padding: 1rem;
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
}

.related-news-item:hover {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    transform: translateX(5px);
}

.related-news-image {
    width: 80px;
    height: 80px;
    border-radius: 12px;
    overflow: hidden;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.related-news-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.related-news-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e0 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #a0aec0;
}

.related-news-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.related-news-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.related-news-meta {
    display: flex;
    align-items: center;
    font-size: 0.8rem;
    color: #718096;
}

/* Category Card */
.category-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    overflow: hidden;
}

.category-header {
    background: linear-gradient(135deg, #718096 0%, #4a5568 100%);
    color: white;
    padding: 1.25rem 1.5rem;
    display: flex;
    align-items: center;
}

.category-header h5 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
}

.category-list {
    padding: 0.5rem;
}

.category-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.25rem;
    border-radius: 12px;
    text-decoration: none;
    color: #2d3748;
    font-weight: 500;
    transition: all 0.3s ease;
    margin-bottom: 0.5rem;
    position: relative;
}

.category-item .arrow {
    margin-left: auto;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
}

.category-item:hover .arrow {
    opacity: 1;
    transform: translateX(0);
}

.category-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    flex-shrink: 0;
}

.category-news:hover {
    background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
}

.category-news .category-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.category-announcement:hover {
    background: linear-gradient(135deg, #4facfe15 0%, #00f2fe15 100%);
}

.category-announcement .category-icon {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.category-event:hover {
    background: linear-gradient(135deg, #f093fb15 0%, #f5576c15 100%);
}

.category-event .category-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.category-guide:hover {
    background: linear-gradient(135deg, #43e97b15 0%, #38f9d715 100%);
}

.category-guide .category-icon {
    background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
}

/* Responsive */
@media (max-width: 768px) {
    .article-title {
        font-size: 1.75rem;
    }

    .related-news-image {
        width: 60px;
        height: 60px;
    }

    .modern-btn {
        font-size: 0.875rem;
        padding: 0.625rem 1.25rem;
    }
}
</style>
@endpush
@endsection
