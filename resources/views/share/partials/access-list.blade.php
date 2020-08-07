@if($existing_shares)
    @foreach($existing_shares as $share)
        @include('share.partials.shared-list-item', ['item' => $share])
    @endforeach
@endif