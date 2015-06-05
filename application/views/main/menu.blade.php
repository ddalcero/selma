	<li><a href="{{ url('main') }}"><i class="fa fa-fw fa-home icon-black"></i> Inicio</a></li>
	@if (Sentry::user()->has_access('realizado'))
	<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-tasks icon-black"></i> Proyectos <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="{{ url('main/realizado') }}"><i class="fa fa-fw fa-tasks icon-black"></i> Realizado</a></li>
		@if (Sentry::user()->has_access('factory'))
		<li><a href="{{ url('main/factory') }}"><i class="fa fa-fw fa-table icon-black"></i> Software Factory</a></li>
		@endif
		@if (Sentry::user()->has_access('mod_realizado'))
		<li class="divider"></li>
		<li><a href="{{ url('main/stickers') }}"><i class="fa fa-fw fa-bookmark icon-black"></i> Stickers activos</a></li>
		<li><a href="{{ url('main/pendientes') }}"><i class="fa fa-fw fa-warning-sign icon-black"></i> Proyectos pendientes</a></li>
		@endif
	</ul>
	</li>
	<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-money icon-black"></i> Facturación <b class="caret"></b></a>
	<ul class="dropdown-menu">
        @if (Sentry::user()->has_access('mod_realizado'))
        <li><a href="{{ url('main/dtesOlga') }}"><i class="fa fa-fw fa-exchange icon-black"></i> Control Softland - OLGA</a></li>
        @endif
		<li><a href="{{ url('main/facturacion') }}"><i class="fa fa-fw fa-credit-card icon-black"></i> Solicitar</a></li>
		@if (Sentry::user()->has_access('is_admin'))
		<li><a href="{{ url('main/gestionfacturas') }}"><i class="fa fa-fw fa-list-ul icon-black"></i> Solicitudes pendientes</a></li>
		@endif
		<li><a href="{{ url('main/dtes') }}"><i class="fa fa-fw fa-file-pdf-o icon-black"></i> DTEs Emitidos</a></li>
        <li><a href="{{ url('main/dtesPendientes') }}"><i class="fa fa-fw fa-usd icon-black"></i> DTEs Pendientes</a></li>
	</ul>
	</li>
	@endif
<!-- TO-DO PENDING
	@if (Sentry::user()->has_access('candidatos'))
	<li><a href="{{ url('candidato') }}"><i class="fa fa-fw fa-user icon-black"></i> Candidatos</a></li>
	@endif
	@if (Sentry::user()->has_access('mis_vacaciones'))
	<li><a href="{{ url('vacaciones') }}"><i class="fa fa-fw fa-list icon-black"></i> Mis Vacaciones</a></li>
	@endif
-->
	@if (Sentry::user()->has_access('rrhh'))
	<li><a href="{{ url('personal') }}"><i class="fa fa-fw fa-user icon-black"></i> Personal</a></li>
	@endif

	@if (Sentry::user()->has_access('is_admin'))
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-fw fa-cogs icon-black"></i> Configuración <b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a href="{{ url('user') }}"><i class="fa fa-fw fa-user icon-black"></i> Usuarios</a></li>
			<li><a href="{{ url('group') }}"><i class="fa fa-fw fa-group icon-black"></i> Grupos</a></li>
			<li><a href="{{ url('main/updateuf') }}"><i class="fa fa-fw fa-money icon-black"></i> Actualiza UF</a></li>
		</ul>
	</li>
	@endif
	<li class="divider"></li>
	<li><a href="{{ url('logout') }}"><i class="fa fa-fw fa-off icon-black"></i> Cerrar sesión</a></li>
