<?php

spl_autoload_extensions(".php"); // comma-separated list
spl_autoload_register();

foreach (glob("system/vendor/*.php") as $filename)
{
	include $filename;
}

if (!is_cli()) {
	die("This script must be ran from the command line");
}

$core = new \system\engine\HF_Core(true);
$core->setupDatabaseConnection();

if (count($argv) == 1) {
	echo "Possible commands are all-clear or adduser";
	exit(0);
}

switch ($argv[1]) {
	case "all-clear":
		\vendor\DB\DB::query("DELETE FROM users");
		\vendor\DB\DB::query("DELETE FROM sessions");
		break;
}
