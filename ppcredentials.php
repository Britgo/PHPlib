<?php

//   Copyright 2017 John Collins

// *****************************************************************************
// PLEASE BE CAREFUL ABOUT EDITING THIS FILE, IT IS SOURCE-CONTROLLED BY GIT!!!!
// Your changes may be lost or break things if you don't do it correctly!
// *****************************************************************************

//   This program is free software: you can redistribute it and/or modify
//   it under the terms of the GNU General Public License as published by
//   the Free Software Foundation, either version 3 of the License, or
//   (at your option) any later version.

//   This program is distributed in the hope that it will be useful,
//   but WITHOUT ANY WARRANTY; without even the implied warranty of
//   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//   GNU General Public License for more details.

//   You should have received a copy of the GNU General Public License
//   along with this program.  If not, see <http://www.gnu.org/licenses/>.

class PPCredentials_error extends Exception {}

class PPCredentials {
	public $Username;
	public $Password;
	public $Signature;
	public $Endpoint;
	public $Url;
	
	public function __construct($u, $p, $s, $e, $url)
	{
		$this->Username = $u;
		$this->Password = $p;
		$this->Signature = $s;
		$this->Endpoint = $e;
		$this->Url = $url;
	}
}

function getppcredentials()
{
	$fh = fopen("/etc/paypal-credentials", "r");
	if (!$fh)
		throw new PPCredentials_error("Cannot open credentials file");
	
	$un = $pw = $sig = $ep = $url = null;

	while ($l = fgets($fh, 1024)) {
		if (!preg_match("/^\s*(\w+)\s*=\s*(.*)\n/", $l, $matches))
				continue;
		$cname = $matches[1];
		$cval = $matches[2];
		switch (strtolower($cname)) {
		case "user":
		case "username":
			$un = $cval;
			break;
		case "password":
			$pw = $cval;
			break;
		case "signature":
			$sig = $cval;
			break;
		case "endpoint";
			$ep = $cval;
			break;
		case "url":
			$url = $cval;
			break;
		}
	}
	fclose($fh);
	if (is_null($un))
		throw new PPCredentials_error("Could not find Username in credentials file");
	if (is_null($pw))
		throw new PPCredentials_error("Could not find Password in credentials file");
	if (is_null($sig))
		throw new PPCredentials_error("Could not find Signature in credentials file");
	if (is_null($ep))
		throw new PPCredentials_error("Could not find Endpoint in credentials file");
	if (is_null($url))
		throw new PPCredentials_error("Could not find URL in credentials file");
	return new PPCredentials($un, $pw, $sig, $ep, $url);
}
?>
