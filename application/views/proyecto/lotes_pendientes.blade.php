@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
	<div class="span9">
		<br/>

		<h3>Lotes</h3>

		<p class="lead">Lotes pendientes de facturación</p>

		<hr/>

		@include('proyecto/detail_lotes_pendientes')

	</div>
</div>
@endsection

@section('css')
@parent
{{-- <style> --}}
.table th.rightCell,
.table td.rightCell,
.table td.column-lot_montant_euro,
.table td.column-lot_montant_uf,
.table td.column-imputado {
  text-align: right;
}
{{-- </style> --}}
@endsection
