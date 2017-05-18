<?php
/*
Project: Github_Scraping

*/

$username = 'username';
$password = 'password';
const cookiefile = 'C:/xampp/htdocs/scrap/github_scraper/cookie.txt';
const POST = true;
const GET = false;

if(! Login($username, $password)){
    die("Unable to Login\n");
}
echo "Successfully Logged In\n";


// FUNCTIONALITY START HERE

function get_words($pattern, $string){

        preg_match($pattern, $string, $match);

        if( isset($match[1]) ) {
            return $match[1];     
        }
        else{
            return false;
         }
}


function Login($username, $password){
    if(CheckLogin()){
        echo "Already Logged In\n";
        return true;
    }
    echo "Trying to Log In\n";
    $url = "https://github.com/login";

    $data = get_source($url, GET);

    $patt_toekn = '@authenticity_token" type="hidden" value="(.*?)"@';

    $utf8 = "✓";
    $token = get_words($patt_toekn, $data);

    $post = [
            'utf8' => $utf8,
            'authenticity_token' => $token,
            'login' => $username,
            'password' => $password,
            'commit' => 'Sign in',
        ];

    $url = "https://github.com/session"; // ON THIS PAGE I HAVE TO POST

    $data = get_source($url, POST, $post);
    
    if(strpos($data,  'href="/login"')){ // HERE HAVE TO CHECK IS IT LOGIN PAGE ? NOT /login
        
        return false;
    }
   
    return true;
}


function CheckLogin(){
    $source = get_source("https://github.com/", GET);
    
    if(strpos($source, 'href="/login"')){
        return false;
    }

    return true;
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
