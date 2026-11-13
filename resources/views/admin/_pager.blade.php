@if ($paginator->hasPages())
    <div class="actions" style="margin-top:16px;justify-content:center">
        @if ($paginator->onFirstPage())
            <span class="btn sm gray" style="opacity:.5">← Prev</span>
        @else
            <a class="btn sm gray" href="{{ $paginator->previousPageUrl() }}">← Prev</a>
        @endif
        <span class="muted">Page {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
        @if ($paginator->hasMorePages())
            <a class="btn sm gray" href="{{ $paginator->nextPageUrl() }}">Next →</a>
        @else
            <span class="btn sm gray" style="opacity:.5">Next →</span>
        @endif
    </div>
@endif
