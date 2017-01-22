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

class Credentials_error extends Exception {}

class Credentials {
	public $Databasename;
	public $Username;
	public $Password;
	
	public function __construct($d, $u, $p)
	{
		$this->Databasename = $d;
		$this->Username = $u;
		$this->Password = $p;
	}
}

function getcredentials($appname)
{
	$fh = fopen("/etc/webdb-credentials", "r");
	if (!$fh)
		throw new Credentials_error("Cannot open credentials file");
	
	$ret = null;
	while ($l = fgets($fh, 1024)) {
		if (!preg_match('/^\s*\[(.*)\]\s*$/', $l, $matches))
			continue;
		if ($matches[1] != $appname)
			continue;
		$db = null;
		$us = null;
		$pw = null;
		while ($l = fgets($fh, 1024))  {
			if (!preg_match("/^\s*(\w+)\s*=\s*(.*)\n/", $l, $matches))
				continue;
			$cname = $matches[1];
			$cval = $matches[2];
			switch (strtolower($cname)) {
			case "database":
				$db = $cval;
				break;
			case "user":
			case "username":
				$un = $cval;
				break;
			case "password":
				$pw = $cval;
				break;
			}
			if (!is_null($db) && !is_null($un) && !is_null($pw))  {
				$ret = new Credentials($db, $un, $pw);
				break 2;
			}
		}
	}
	fclose($fh);
	if (is_null($ret))
		throw new Credentials_error("Could not find credentials for $appname");
	return $ret;
}
?>
