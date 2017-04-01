<?php
/*
DATE: 31.03.17
AUTHOR: PROKAWSAR
*/
$url = "http://examples.winautomation.com/view_person/71";  // FOR DEMO PURPOSE
$i = 0;

$handle = curl_init($url);
curl_setopt($handle, CURLOPT_POST, true);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($handle);

$patt_for_title = '@label">(.{1,10}:?)<@';
$patt_for_data = '@>(.{1,20}[^:])<\/div>@';
$titles = array();
$datas = array();

$link = 0;
$num = preg_match_all($patt_for_title, $data, $matches);
$num2 = preg_match_all($patt_for_data, $data, $matches2);

$num = $num>$num2?$num:$num2; // HANDLEING IF THERE IS MISSING ANY DATA

    while($link < $num-1){
        if($matches[1][$link] != ""){
            echo $matches[1][$link]." ";
        }
        if($matches2[1][$link] != ""){
            echo $matches2[1][$link]."\n";
        }

        $link++;
    }
