<?php
function talkToNode($addrEnd, $params, &$returns)
{
    $ch = curl_init();

    //curl_setopt($ch, CURLOPT_URL, '127.0.0.1:7777' . $addrEnd . "?" . http_build_query($params));
    curl_setopt($ch, CURLOPT_URL, $_SERVER['SERVER_NAME'] . ':7777' . $addrEnd . "?" . http_build_query($params));

    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_HTTPGET, true);

    /*$pf = $params;

    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($pf));*/
    $returns = curl_exec($ch);
    curl_close($ch);
}