<?php
switch(ENV) {
    case 'dev':
        return include 'config-dev.php';
        break;
    case 'prod':
        return include 'config-prod.php';
        break;
    default:
        return include 'config-dev.php';
        break;
}
