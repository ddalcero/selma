

{{ Table::striped_bordered_hover_condensed_open() }}
{{ Table::headers('#', 'Fono', 'Descripcion', 'Anexo') }}

{{ Table::body($llamadas) }}

{{ Table::close() }}

@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    <div class="span3">
        <div class="well sidebar-nav">
            <ul class="nav nav-list">
                <li class="nav-header">Menu</li>
                <li><a href="#donation_modal" data-toggle="modal">Add new Donation</a></li>
                <li><a href="#package_modal" data-toggle="modal">Create new Package</a></li>
                <li><a href="#transport_modal" data-toggle="modal">Register new Transport</a></li>
                <li><a href="#stock_modal" data-toggle="modal">Insert new Stock Type</a></li>
                <li><a href="{{ url('logout') }}">Logout</a></li>
            </ul>
        </div><!--/.well -->
        <div class="well sidebar-nav" id="action_well">
            <ul class="nav nav-list" id="action_container">
                <li class="nav-header">Updates (Logged in as {{Sentry::user()->username}})</li>
            </ul>
        </div>
    </div><!--/span-->
    <div class="span9">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#donations_table_container" data-toggle="tab">Donations</a></li>
            <li><a href="#packages_table_container" data-toggle="tab">Packages</a></li>
            <li><a href="#transports_table_container" data-toggle="tab">Transports</a></li>
            <li><a href="#stocks_table_container" data-toggle="tab">Stock Types</a></li>
        </ul>
        <div class="tab-content">
            @include('plugins/donations_table')
            @include('plugins/packages_table')
            @include('plugins/transports_table')
            @include('plugins/stocks_table')
        </div>
    </div>
</div>
@endsection


@section('js')
@parent
{{-- <script> --}}

{{-- </script> --}}
@endsection

@section('css')
@parent
{{-- <style> --}}
#action_container {
    padding: 0px;
    font-size: 12px;
}
#action_well {
}
#action_container > li.nav-header {
    padding-left: 30px;
}
.tablediv {
    margin-bottom: 40px;
}
#packs {
    width: 280px;
}
{{-- </style> --}}
@endsection