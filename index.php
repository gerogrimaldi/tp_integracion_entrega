<?php
if (session_status() === PHP_SESSION_NONE) {session_start();}
require_once __DIR__.'/includes/config.php';
require_once __DIR__.'/model/Page.php';
require_once __DIR__.'/controller/PageController.php';

    $oPage=new Page();
    $oPage->setBody($body);
    echo $oPage->getHtml();
