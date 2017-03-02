@extends('management-layout')

@section('sub-header')
        
    <a href="{{route('administration.index')}}" class="parent">{{trans('administration.page_title')}}</a> {{trans('administration.menu.network')}}

@stop

@section('action-menu')


<div class="action-group">
    {{-- <a href="{{ route('administration.users.create') }}" class="button">
        <span class="btn-icon icon-social-white icon-social-white-ic_person_add_white_24dp"></span>Create User
    </a> --}}
</div>


@stop

@section('content')

<div class="row">

    <div class="two columns">

        @include('administration.adminmenu')

    </div>

    <div class="ten columns ">

        <div class="widget klink-network">

            <h5>{{trans('administration.network.klink_net_title')}}</h5>

            <span class="badge {{$klink_network_connection}}">{{trans('administration.network.klink_status.' . $klink_network_connection)}}</span>

            @if($klink_network_connection_error)
                {{$klink_network_connection_error->getMessage()}}
            @endif
        </div>
        
        <p></p>

        <div class="network">

            <h5>{{trans('administration.network.net_cards_title')}}</h5>

            @forelse($network_cards as $card)

                @if(isset($card['interface']))

                    <div class="card">

                        <strong><span class="network-icon {{strtolower($card['type'])}}"></span> {{$card['interface']}}</strong>

                        <span class="type">{{$card['type']}}</span>

                        @if(isset($card['ip_address']) && !empty($card['ip_address']))

                            <span class="address">{{trans('administration.network.current_ip', ['ip' => $card['ip_address']])}}</span>

                        @endif

                    </div>

                @endif

            @empty

                @if(isset($network_config) && !is_null($network_config))

                    <p>{{trans('administration.network.cards_problem')}}</p>

                    <pre>{{$network_config}}</pre>

                @else

                    <p>{{trans('administration.network.no_cards')}}</p>

                @endif

            @endforelse

        </div>

        

    </div>

</div>

@stop