<?php

function admin_auth()
{
    if (empty($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
        header('Location: /admin/login');
        exit;
    }
}


function customer_auth()
{
    if (empty($_SESSION['customer_logged_in'])) {
        header('Location: /customer/login');
        exit;
    }
}

