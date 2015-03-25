@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
	<div class="span9">
		<br/>

		<h3>Lotes</h3>

		<p class="lead">Lotes de facturación</p>

		<dl class="dl-horizontal">
			<dt>Cliente</dt>
			<dd>{{ $proyecto[0]['clt_nom'] }}</dd>
			<dt>Proyecto</dt>
			<dd>{{ $proyecto[0]['spj_libelle'] }}</dd>
		</dl>

		<hr/>

		@include('proyecto/detail_lotes')

		<br/>
		<p>
			{{ HTML::link('main/facturacion','Volver a proyectos',array('class'=>'btn btn-primary')) }}
		</p>
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
