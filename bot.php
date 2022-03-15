<?php

include "__autoload.php";
include "utils.php";
$db_config = include "config.db.php";
include "tg.funcs.php";

use \ThinBuilder\ThinBuilder;
$tb = new ThinBuilder($db_config);

