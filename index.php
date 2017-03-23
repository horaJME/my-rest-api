<?php

use Phalcon\Mvc\Micro;

//New microapp
$app = new Micro();

//get method
$app->get(
	"/api/OTP",
	function (){
		return "GETTING";
	}
);

//post
$app->post(
	"/api/OTP",
	function () use ($app) {
		return "POSTING";
	}
);

$app->handle();
