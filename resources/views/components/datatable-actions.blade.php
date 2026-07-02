<div class="d-flex justify-content-center gap-2">

    @if (in_array('show', $actions))
        <a href="{{ route($resource . '.show', $id) }}" class="btn btn-info btn-sm">
            <i class="fa fa-eye"></i>
        </a>
    @endif

    @if (in_array('edit', $actions))
        <a href="{{ route($resource . '.edit', $id) }}" class="btn btn-warning btn-sm">
            <i class="fa fa-edit"></i>
        </a>
    @endif

    @if (in_array('delete', $actions))
        <form action="{{ route($resource . '.destroy', $id) }}" method="POST" style="display:inline-block;">
            @csrf
            @method('DELETE')

            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">
                <i class="fa fa-trash"></i>
            </button>
        </form>
    @endif

</div>
