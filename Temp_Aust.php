<?php
/*
Project: Australia_Scraping

*/

const cookiefile = 'cookie.txt';
const POST = true;
const GET = false;

    $pattern = '@_token"\svalue="(.+?)"@';

    $url = "https://www.businesses2sell.com.au/australia/act";
    $data = get_source($url, GET);
    $token = get_words($pattern, $data);

    $post = [
        '_token' => $token,        
        'iteration' => '2', // for this time
        'feed' => 'search',
        'state' => 'ACT',
        'region' => '',
        'city' => '',
        'category' => '',
        'pricing' => '',
    ];

    $data = get_source('https://www.businesses2sell.com.au/data/fetch', POST, $post);
    
    echo $data;

# FUNCTIONALITY START HERE

function get_words($pattern, $string){

        preg_match($pattern, $string, $match);

        if( isset($match[1]) ) {
            return $match[1];     
        }
        else{
            return false;
         }
}


function get_source($url, $method, $fields =  null){
    
    $handle = curl_init($url);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_COOKIEFILE, cookiefile); 
    curl_setopt($handle, CURLOPT_COOKIEJAR, cookiefile);
    curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);

    if($method == true){
        
        curl_setopt($handle, CURLOPT_POST, true); 
        curl_setopt($handle, CURLOPT_HTTPGET, false);   
        curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($fields));

        $source = curl_exec($handle);
        
    }else{
        curl_setopt($handle, CURLOPT_HTTPGET, true);
        curl_setopt($handle, CURLOPT_POST, false); 
    }
    $source = curl_exec($handle);

    curl_close($handle); 
    
    return $source;
}
