@unless ($breadcrumbs->isEmpty())
    @foreach ($breadcrumbs as $breadcrumb)
        @if (
            $breadcrumb->url 
            && ! $loop->last
        )
            <a href="{{ $breadcrumb->url }}">{{ $breadcrumb->title }}</a> /
        @else
            {{ $breadcrumb->title }}
        @endif
    @endforeach
@endunless