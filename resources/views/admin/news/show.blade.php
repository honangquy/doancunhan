@extends('layouts.admin')

@section('title', 'Chi tiết tin tức')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Chi tiết tin tức</h1>
        <div class="btn-group">
            <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-2"></i>Quay lại
            </a>
            <a href="{{ route('admin.news.edit', $news->news_id) }}" class="btn btn-primary">
                <i class="fas fa-edit me-2"></i>Chỉnh sửa
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-8">
            <div class="card mb-4">
                @if($news->cover_image)
                    <img src="{{ asset('storage/' . $news->cover_image) }}" class="card-img-top" alt="{{ $news->title }}">
                @endif
                <div class="card-body">
                    <h2 class="card-title">{{ $news->title }}</h2>
                    
                    <div class="mb-3">
                        <span class="badge bg-{{ $news->status_color }}">{{ $news->status_name }}</span>
                        <span class="badge bg-info">{{ $news->category_name }}</span>
                        @if($news->is_featured)
                            <span class="badge bg-warning">Nổi bật</span>
                        @endif
                    </div>

                    @if($news->summary)
                        <div class="alert alert-light">
                            <strong>Tóm tắt:</strong> {{ $news->summary }}
                        </div>
                    @endif

                    <div class="news-content">
                        {!! nl2br(e($news->content)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <th width="120">Slug:</th>
                            <td><code>{{ $news->slug }}</code></td>
                        </tr>
                        <tr>
                            <th>Hội thảo:</th>
                            <td>{{ $news->conference->title ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Trạng thái:</th>
                            <td><span class="badge bg-{{ $news->status_color }}">{{ $news->status_name }}</span></td>
                        </tr>
                        <tr>
                            <th>Danh mục:</th>
                            <td>{{ $news->category_name }}</td>
                        </tr>
                        <tr>
                            <th>Người tạo:</th>
                            <td>{{ $news->creator->full_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo:</th>
                            <td>{{ $news->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($news->updated_by)
                        <tr>
                            <th>Người sửa:</th>
                            <td>{{ $news->updater->full_name ?? 'N/A' }}</td>
                        </tr>
                        @endif
                        <tr>
                            <th>Cập nhật:</th>
                            <td>{{ $news->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($news->published_at)
                        <tr>
                            <th>Xuất bản:</th>
                            <td>{{ $news->published_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            @if($news->status == 'PUBLISHED')
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Xem công khai</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('news.show', $news->slug) }}" target="_blank" class="btn btn-success w-100">
                        <i class="fas fa-external-link-alt me-2"></i>Xem trang công khai
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
