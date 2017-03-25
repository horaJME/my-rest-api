<?php

use Phalcon\Mvc\Micro;
use Phalcon\Http\Response;
use Phalcon\Http\Request;

//New microapp
$app = new Micro();

//IDENTIFICATION

//GET METHOD USER
//Searching if users OTP list exists
$app->get(
	"/api/user/{user}",
	function ($user) use ($app) {
		//Checking for user
		$filename = "users/".$user.".txt";
		if (file_exists($filename)) {
			echo "User exists";
		} else {
			echo "Error #001 - User does not exist";
		}
	}
);

//POST METHOD PIN
//Sending User and his PIN if his OTP list exists
$app->post(
	"/api/PIN",
	function () use ($app)	{
		
		$request = new Request();
		$response = new Response();
		$status = 'xxx';
		
		//Posted data
		$json = $request->getPost();
		$json = key($json);
		$json = str_replace("'", "",$json);
		
		//Posted information array
		//Unpacking info
		//1. Username 2. PIN
		$info = [
			"user" => substr($json,6,7),
			"PIN" => substr($json,-5,4),
		];
		
		//Reading file
		$filename = "users/".$info["user"].".txt";
		$userFile = fopen($filename, "rw") or die("Unable to open file!");
		$file = fread($userFile,filesize($filename));
		$file = json_decode($file);
		fclose($userFile);
		
		//Checking if user already finished process 
		if (empty($file->{"PIN"})){
			//Preparing JSON for writing
			$status = "PIN Added";
			$file->{"PIN"} = $info["PIN"];
			$file = json_encode($file);

			//Writing PIN into file
			$writeFile = fopen($filename, "w") or die("Unable to open file!");
			fwrite($writeFile, $file);
			fclose($writeFile);
			
		}else {
			$status = "Error #002 - User already finished ID process";
		}
		
		//Response
		$response->setJsonContent([
                "status" => $status,
                "data"   => $info,
            ]);		
		return $response;
	}
);

//GET METHOD OTP
//Getting OTP list if PIN is set
$app->get(
	"/api/OTP/{user}",
	function ($user) use ($app)	{
		
		
		
		return $OTP;
	}
);

//AUTHENTIFICATION

$app->handle();
