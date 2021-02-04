<?php
if (!isset($_GET['step']))
    require_once('./order/form.php');
else if ($_GET['step'] === "2")
    require_once('./order/confirm.php');
else if ($_GET['step'] === "3")
    require_once('./order/completed.php');
else
    require_once('./order/form.php');
