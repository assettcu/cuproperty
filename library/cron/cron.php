<?php

try {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,"http://compass.colorado.edu/cuproperty/ajax/cron");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $server_output = curl_exec ($ch);
    curl_close ($ch);
    if($server_output === false) {
        throw new Exception("Cron job was unsuccessful.");
    }
}
catch(Exception $e) {
    echo $e->getMessage();
    exit;
}

?>
