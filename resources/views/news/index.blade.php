@extends('layouts.app')

@section('title', 'Tin tức & Sự kiện')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="bg-primary text-white p-4 rounded mb-4">
                <h1 class="h3 mb-2">
                    <i class="fas fa-newspaper"></i> Tin tức & Sự kiện
                </h1>
                <p class="mb-0 text-white-50">Cập nhật tin tức mới nhất về các hội thảo và hoạt động khoa học tại HUIT</p>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('news.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Danh mục</label>
                            <select name="category" class="form-select">
                                <option value="">Tất cả</option>
                                <option value="NEWS" {{ request('category') == 'NEWS' ? 'selected' : '' }}>Tin tức</option>
                                <option value="ANNOUNCEMENT" {{ request('category') == 'ANNOUNCEMENT' ? 'selected' : '' }}>Thông báo</option>
                                <option value="EVENT" {{ request('category') == 'EVENT' ? 'selected' : '' }}>Sự kiện</option>
                                <option value="GUIDE" {{ request('category') == 'GUIDE' ? 'selected' : '' }}>Hướng dẫn</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Hội thảo</label>
                            <select name="conference_id" class="form-select">
                                <option value="">Tất cả</option>
                                @foreach(\App\Models\HoiThao::orderBy('title')->get() as $conf)
                                    <option value="{{ $conf->conference_id }}" {{ request('conference_id') == $conf->conference_id ? 'selected' : '' }}>
                                        {{ $conf->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Tìm kiếm</label>
                            <input type="text" name="search" class="form-control" placeholder="Tìm theo tiêu đề, nội dung..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i> Lọc
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Featured News -->
            @if(isset($featuredNews) && $featuredNews->count() > 0)
            <div class="row mb-4">
                @foreach($featuredNews->take(2) as $item)
                <div class="col-md-6 mb-3">
                    <div class="card border-warning shadow-sm h-100">
                        <div class="badge bg-warning text-dark position-absolute" style="top: 10px; left: 10px; z-index: 1;">
                            <i class="fas fa-star"></i> Nổi bật
                        </div>
                        @if($item->cover_image)
                        <img src="{{ $item->cover_image_url }}" class="card-img-top" alt="{{ $item->title }}" style="height: 250px; object-fit: cover;">
                        @endif
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge bg-{{ $item->status_color }}">{{ $item->category_name }}</span>
                                @if($item->conference)
                                    <span class="badge bg-secondary">{{ $item->conference->title }}</span>
                                @endif
                            </div>
                            <h5 class="card-title">
                                <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none text-dark">
                                    {{ $item->title }}
                                </a>
                            </h5>
                            <p class="card-text text-muted">{{ $item->excerpt }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">
                                    <i class="far fa-calendar"></i> {{ $item->published_at->format('d/m/Y') }}
                                </small>
                                <a href="{{ route('news.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">
                                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            @endif

            <!-- Regular News List -->
            <div class="row">
                @forelse($news as $item)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm hover-shadow">
                        @if($item->cover_image)
                        <img src="{{ $item->cover_image_url }}" class="card-img-top" alt="{{ $item->title }}" style="height: 200px; object-fit: cover;">
                        @else
                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-newspaper fa-4x text-muted"></i>
                        </div>
                        @endif
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge bg-{{ $item->status_color }}">{{ $item->category_name }}</span>
                                @if($item->conference)
                                    <span class="badge bg-secondary">{{ $item->conference->title }}</span>
                                @endif
                            </div>
                            <h5 class="card-title">
                                <a href="{{ route('news.show', $item->slug) }}" class="text-decoration-none text-dark">
                                    {{ Str::limit($item->title, 60) }}
                                </a>
                            </h5>
                            <p class="card-text text-muted small">{{ $item->excerpt }}</p>
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <small class="text-muted">
                                    <i class="far fa-calendar"></i> {{ $item->published_at->format('d/m/Y') }}
                                </small>
                                <a href="{{ route('news.show', $item->slug) }}" class="btn btn-sm btn-outline-primary">
                                    Đọc thêm
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i> Chưa có tin tức nào được xuất bản.
                    </div>
                </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($news->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $news->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.hover-shadow {
    transition: all 0.3s ease;
}
.hover-shadow:hover {
    transform: translateY(-5px);
    box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
}
</style>
@endsection
