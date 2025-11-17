@extends('layouts.chair')

@section('title', 'Tạo tin tức mới')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Tạo tin tức mới</h1>
        <a href="{{ route('chair.news.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('chair.news.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @include('chair.news.form')
                
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('chair.news.index') }}" class="btn btn-secondary">Hủy</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane me-2"></i>Gửi tin tức
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
