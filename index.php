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
		
		//Initial status and counter for using OTPs		
		$status = 'xxx';
		$counter = 0;
		
		//Posted data
		$json = $request->getPost();
		$json = key($json);
		$json = str_replace("'", "",$json);
		
		//Posted information array
		//Unpacking info
		//1. Username 2. PIN
		$info = [
			"PIN" => substr($json,5,4),
			"user" => substr($json,-8,7),
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
			$file->{"counter"} = $counter;
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
		//Opening document
		$filename = "users/".$user.".txt";
		$userFile = fopen($filename, "rw") or die("Unable to open file!");
		$file = fread($userFile,filesize($filename));
		
		//Preparing list of OTPs
		$OTP = $file;
		
		//Sending OTP list
		return $OTP;
	}
);

//AUTHENTIFICATION

//POST METHOD AUTH
//Verifying given credentials, checking OTP 
$app->post(
	"/api/auth",
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
		$postInfo = [
			"user" => substr($json,6,7),
			"OTP" => substr($json,-5,4),
		];
		
		//Reading file
		$filename = "users/".$postInfo["user"].".txt";
		$userFile = fopen($filename, "rw") or die("Unable to open file!");
		$file = fread($userFile,filesize($filename));
		$file = json_decode($file);
		$OTPlist[] = $file->{"OTPlist"};
		$FileOTP = json_decode(json_encode($OTPlist[0][$file->{"counter"}]));
		fclose($userFile);

		//Checking if OTP and counter match
		if ($FileOTP->{"OTP"} == $postInfo["OTP"]){
			//Preparing JSON for writing
			$status = "Authentication successful";
			
			//Updating counter, start from 0 after 9
			$file->{"counter"} += 1;
			$file->{"counter"} = $file->{"counter"}%10;
			$file = json_encode($file);

			//Writing updated counter into file
			$writeFile = fopen($filename, "w") or die("Unable to open file!");
			fwrite($writeFile, $file);
			fclose($writeFile);
			
		}else {
			$status = "Error #003 - OTPs doesnt match, Authentication failed";
		}
		
		//Response
		$response->setJsonContent([
                "status" => $status,
                "data"   => $postInfo,
            ]);		
		return $response;
	}
);


$app->handle();
