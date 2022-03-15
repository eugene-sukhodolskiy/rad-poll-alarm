<?php

// classes autoload

spl_autoload_register(function($classname){
	$class_file_path = str_replace("\\", "/", $classname) . ".php";
	if(file_exists($class_file_path)){
		include_once $class_file_path;
	}
});