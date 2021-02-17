<?php
if (!isset($_GET['step']))
    require_once('./signup/order_consent.php');
else if ($_GET['step'] === "2")
    require_once('./signup/order_form.php');
else if ($_GET['step'] === "3")
    require_once('./signup/order_confirm.php');
else if ($_GET['step'] === "4")
    require_once('./signup/order_completed.php');
else
    require_once('./signup/order_consent.php');
