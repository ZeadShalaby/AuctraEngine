@props([
    'showFileInput' => false,
])

<div class="card mb-4">

    <div class="card-header">
        {{ $header ?? '' }}
    </div>

    <div class="card-body">
        {{ $description ?? '' }}

        <form
            {{ $attributes->merge([
                'enctype' => $showFileInput ? 'multipart/form-data' : null,
            ]) }}>
            @csrf

            {{ $slot }}

            @if ($showFileInput)
                <div class="form-group mt-3">
                    <label class="form-label">Upload File</label>
                    <input class="form-control" type="file" name="file">
                </div>
            @endif

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">
                    Submit
                </button>

                <button type="button" onclick="history.back()" class="btn btn-danger">
                    Cancel
                </button>
            </div>

        </form>
    </div>
</div>
