	<nav class="sidebar">
	<div class="sidebar-header">
	<a href="#" class="sidebar-brand">
	   Cloud<span>Extractors</span>
	</a>
	<div class="sidebar-toggler not-active">
		<span></span>
		<span></span></span>
	</div>
	</div>
	<div class="sidebar-body ps">
	<ul class="nav">
		<li class="nav-item nav-category">Scrapers</li>
		<?php fields::printSidebar(); ?>
		<li class="nav-item nav-category">Main</li>
		<li class="nav-item">
		<a href="index" class="nav-link">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book link-icon"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>	
			<span class="link-title">Index</span>
		</a>
		</li>
		<?php if(user::rankIsMax(2)) { ?>
		<li class="nav-item">
		<a href="/admin/" class="nav-link">
			<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-book link-icon"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"></path><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"></path></svg>	
			<span class="link-title">Back to admin panel</span>
		</a>
		</li>
		<?php } ?>
	</ul>
	<div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 0px;"></div></div></div>
</nav>