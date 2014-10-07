	<div class="span3">
		<div class="well sidebar-nav">
			<ul class="nav nav-list">
				<li class="nav-header">Menu</li>
				@include('main/menu')
			</ul>
		</div><!--/.well -->
		<div class="well sidebar-nav" id="action_well">
			<ul class="nav nav-list" id="action_container">
				<li class="nav-header">usuario: {{ HTML::link('password',Sentry::user()->get('metadata.first_name').' '.Sentry::user()->get('metadata.last_name')) }} </li>
			</ul>
		</div>
	</div><!--/span-->