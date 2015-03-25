@foreach ($personas as $persona)
| {{ $persona['dia'] }} | {{ $persona['nombre'] }} ({{ $persona['anyos'] }})
@endforeach