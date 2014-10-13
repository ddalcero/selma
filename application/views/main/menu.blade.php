	<li><a href="{{ url('main') }}"><i class="icon-fixed-width icon-home icon-black"></i> Inicio</a></li>
	@if (Sentry::user()->has_access('realizado'))
	<li class="dropdown">
	<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-fixed-width icon-tasks icon-black"></i> Proyectos <b class="caret"></b></a>
	<ul class="dropdown-menu">
		<li><a href="{{ url('main/realizado') }}"><i class="icon-fixed-width icon-tasks icon-black"></i> Realizado</a></li>
		<li><a href="{{ url('main/facturacion') }}"><i class="icon-fixed-width icon-money icon-black"></i> Facturación</a></li>
		@if (Sentry::user()->has_access('factory'))
		<li><a href="{{ url('main/factory') }}"><i class="icon-fixed-width icon-table icon-black"></i> Software Factory</a></li>
		@endif
		@if (Sentry::user()->has_access('mod_realizado'))
		<li class="divider"></li>
		<li><a href="{{ url('main/stickers') }}"><i class="icon-fixed-width icon-bookmark icon-black"></i> Stickers activos</a></li>
		<li><a href="{{ url('main/pendientes') }}"><i class="icon-fixed-width icon-warning-sign icon-black"></i> Proyectos pendientes</a></li>
		@endif
	</ul>
	</li>
	@endif

	@if (Sentry::user()->has_access('candidatos'))
	<li><a href="{{ url('candidato') }}"><i class="icon-fixed-width icon-money icon-black"></i> Candidatos</a></li>
	@endif

	@if (Sentry::user()->has_access('mis_vacaciones'))
	<li><a href="{{ url('vacaciones') }}"><i class="icon-fixed-width icon-list icon-black"></i> Mis Vacaciones</a></li>
	@endif
	@if (Sentry::user()->has_access('rrhh'))
	<li><a href="{{ url('personal') }}"><i class="icon-fixed-width icon-user icon-black"></i> Personal</a></li>
	@endif

	@if (Sentry::user()->has_access('is_admin'))
	<li class="dropdown">
		<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-fixed-width icon-cogs icon-black"></i> Configuración <b class="caret"></b></a>
		<ul class="dropdown-menu">
			<li><a href="{{ url('user') }}"><i class="icon-fixed-width icon-user icon-black"></i> Usuarios</a></li>
			<li><a href="{{ url('group') }}"><i class="icon-fixed-width icon-group icon-black"></i> Grupos</a></li>
			<li><a href="{{ url('main/updateuf') }}"><i class="icon-fixed-width icon-money icon-black"></i> Actualiza UF</a></li>
		</ul>
	</li>
	@endif
	<li class="divider"></li>
	<li><a href="{{ url('logout') }}"><i class="icon-fixed-width icon-off icon-black"></i> Cerrar sesión</a></li>
