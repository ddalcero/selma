@layout('layouts/main_fluid')

@section('content')
<div class="row-fluid">
    @include('main/left')
    <div class="span9">
        <br/>
			<div class="row-fluid">
				<div class="span12">
					<h3>Candidatos</h3>
					<p class="lead">{{ $title }}</p>
					<div class="row-fluid">
						<div class="span8">
							{{ Table::striped_condensed_open() }}
							<tr>
								<td>Candidato</td>
								<td>{{ ($candidato->genero)?'Sra.':'Sr.' }} {{ $candidato->nombre }} {{ $candidato->apellidos }}</td>
							</tr>
							<tr>
								<td>RUT</td>
								<td>{{ $candidato->rut }}</td>
							</tr>
							<tr>
								<td>Puesto</td>
								<td>{{ $candidato->puesto }}</td>
							</tr>
							<tr>
								<td>Fecha ingreso</td>
								<td>{{ ViewFormat::DateFromDB($candidato->fecha) }}</td>
							</tr>
							<tr>
								<td>Salario líquido</td>
								<td>{{ ViewFormat::NFL($candidato->liquido) }}</td>
							</tr>
							<tr>
								<td>Plazo</td>
								<td>{{ $salario->plazo }}</td>
							</tr>
							<tr>
								<td>{{ ($check)?'Valor cheque día':'Asignación colación' }}</td>
								<td>{{ ViewFormat::NFL($valor) }}</td>
							</tr>
							{{ Table::close() }}
						</div>
						<div class="span4">
							{{ Button::link(URL::to_route('edit_candidato',array($candidato->id)),'Modificar') }} 
							{{ Button::link(URL::to_route('pdf_candidato',array($candidato->id)),'PDF',array('target'=>'_new')) }}
						</div>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12" id="datosSalario">
				@if ($salario->liquido > 0)
				@include('tarifas.detalle')
				@endif
				{{ Button::link(URL::to_route('candidatos'),'Lista de candidatos') }}
				</div>
			</div>
    </div>
</div>
@endsection
