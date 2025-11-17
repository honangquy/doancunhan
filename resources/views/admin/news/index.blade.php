@extends('layouts.admin')

@section('title', 'Quản lý Tin tức & Sự kiện')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Quản lý Tin tức & Sự kiện</h1>
        <a href="{{ route('admin.news.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Tạo tin mới
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.news.index') }}" class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="Tìm kiếm..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">Tất cả danh mục</option>
                        <option value="NEWS" {{ request('category') == 'NEWS' ? 'selected' : '' }}>Tin tức</option>
                        <option value="ANNOUNCEMENT" {{ request('category') == 'ANNOUNCEMENT' ? 'selected' : '' }}>Thông báo</option>
                        <option value="EVENT" {{ request('category') == 'EVENT' ? 'selected' : '' }}>Sự kiện</option>
                        <option value="GUIDE" {{ request('category') == 'GUIDE' ? 'selected' : '' }}>Hướng dẫn</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Bản nháp</option>
                        <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>Chờ duyệt</option>
                        <option value="PUBLISHED" {{ request('status') == 'PUBLISHED' ? 'selected' : '' }}>Đã xuất bản</option>
                        <option value="ARCHIVED" {{ request('status') == 'ARCHIVED' ? 'selected' : '' }}>Lưu trữ</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="conference_id" class="form-select">
                        <option value="">Tất cả hội thảo</option>
                        @foreach($conferences as $conf)
                            <option value="{{ $conf->conference_id }}" {{ request('conference_id') == $conf->conference_id ? 'selected' : '' }}>
                                {{ $conf->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Lọc</button>
                </div>
            </form>
        </div>
    </div>

    <!-- News List -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="60">Ảnh</th>
                            <th>Tiêu đề</th>
                            <th>Danh mục</th>
                            <th>Hội thảo</th>
                            <th>Trạng thái</th>
                            <th>Người tạo</th>
                            <th>Ngày tạo</th>
                            <th width="150">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($news as $item)
                            <tr>
                                <td>
                                    @if($item->cover_image)
                                        <img src="{{ asset('storage/' . $item->cover_image) }}" alt="{{ $item->title }}" class="img-thumbnail" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-center" style="width: 50px; height: 50px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $item->title }}</strong>
                                    @if($item->is_featured)
                                        <span class="badge bg-warning ms-2">Nổi bật</span>
                                    @endif
                                </td>
                                <td>{{ $item->category_name }}</td>
                                <td>{{ $item->conference->title ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-{{ $item->status_color }}">{{ $item->status_name }}</span>
                                </td>
                                <td>{{ $item->creator->full_name ?? 'N/A' }}</td>
                                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.news.show', $item->news_id) }}" class="btn btn-info" title="Xem">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.news.edit', $item->news_id) }}" class="btn btn-warning" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.news.destroy', $item->news_id) }}" method="POST" class="d-inline" 
                                              onsubmit="return confirm('Bạn có chắc muốn xóa tin này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">Không có tin tức nào</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $news->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
