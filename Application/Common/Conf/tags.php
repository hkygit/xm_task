<?php
return array(
	'app_begin' => array('Home\\Behaviors\\CheckFetchLangBehavior'),
	'view_filter' => array('Behavior\TokenBuildBehavior'),
);