@extends('layouts.app')

@section('content')
    <div class="hero-section text-center">
        <div class="container">
            <h2 class="fw-bold">جمع الدورات التعليمية من يوتيوب</h2>
            <p class="opacity-75">أدخل التصنيفات واضغط ابدأ - النظام سيجمع الدورات تلقائياً باستخدام الذكاء الاصطناعي</p>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="search-box">
                    <form id="fetch-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-8">
                                <textarea name="categories" class="form-control" rows="5" placeholder="أدخل التصنيفات (كل تصنيف في سطر جديد)"></textarea>
                            </div>
                            <div class="col-md-4 d-flex flex-column justify-content-center gap-2">
                                <button type="submit" class="btn btn-primary-custom text-white w-100">
                                    <i class="bi bi-play-fill"></i> ابدأ الجمع
                                </button>
                                <button type="button" class="btn btn-outline-secondary w-100">إيقاف</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold">الدورات المكتشفة</h4>
                <div id="filter-badges">
                </div>
            </div>

            <div class="row" id="playlists-container">
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {

            function renderPlaylist(pl) {
                return `
                        <div class="col-md-3 mb-4">
                            <div class="course-card shadow-sm">
                                <div class="thumb-container">
                                    <img src="${pl.thumbnail || ''}" class="w-100" style="height:150px; object-fit:cover;">
                                    <span class="video-count-badge">${pl.video_count || 0} درس</span>
                                    <span class="duration-badge"><i class="bi bi-clock"></i> 5 ساعات</span>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-2" style="height: 40px; overflow: hidden;">${pl.title}</h6>
                                    <div class="d-flex align-items-center mb-3">
                                        <small class="text-muted"><i class="bi bi-person"></i> ${pl.channel_name || 'قناة تعليمية'}</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="category-badge">${pl.category || 'عام'}</span>
                                        <small class="text-muted" style="font-size:10px;">${pl.views || '0'} مشاهدة</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
            }

            $('#fetch-form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let data = form.serialize();

                $('#playlists-container').html('');
                $('#loading').show();
                $('#alert-container').html('');

                $.ajax({
                    url: "{{ route('fetch') }}",
                    method: 'POST',
                    data: data,
                    success: function(res) {
                        $('#loading').hide();

                        if (Array.isArray(res)) {
                            res.forEach(categoryData => {
                                if (categoryData.playlists && categoryData.playlists
                                    .length) {
                                    categoryData.playlists.forEach(pl => {
                                        $('#playlists-container').append(
                                            renderPlaylist(pl));
                                    });
                                }
                            });
                        } else if (res.playlists) {
                            res.playlists.forEach(pl => {
                                $('#playlists-container').append(renderPlaylist(pl));
                            });
                        }

                        if ($('#playlists-container').children().length === 0) {
                            $('#alert-container').html(
                                '<div class="alert alert-info">No playlists found.</div>');
                        }
                    },
                    error: function(err) {
                        $('#loading').hide();
                        let message = err.responseJSON?.error || 'Something went wrong!';
                        $('#alert-container').html('<div class="alert alert-danger">Error: ' +
                            message + '</div>');
                    }
                });
            });

        });
    </script>
@endpush
