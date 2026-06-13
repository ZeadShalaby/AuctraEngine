<div class="col-md-4 mb-4">
    <div class="card card-custom shadow-sm">
        <img src="{{ $playlist->thumbnail }}" class="thumbnail" alt="{{ $playlist->title }}">
        <div class="card-body">
            <h5>{{ $playlist->title }}</h5>
            <p class="text-muted small">{{ $playlist->channel_name }}</p>
            <p class="small">{{ Str::limit($playlist->description, 100) }}</p>
            <span class="badge bg-primary">{{ $playlist->category }}</span>
        </div>
    </div>
</div>