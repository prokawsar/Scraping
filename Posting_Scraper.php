<?php
ini_set('max_execution_time', 500);
include 'conn.php';

//$data = array('city'=>'', 'state' => 'AL', 'submit'=>'submit'); // THIS IS ONE WAY
$state = array();

$url = "http://examples.winautomation.com/view_people";  // SCRAPING FROM THIS PAGE OF ALL STATE SELECTED
$i = 0;

$handle = curl_init($url);
curl_setopt($handle, CURLOPT_POST, true);
curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);

$data = curl_exec($handle);

// GETTING DROPDOWN LIST OPTIONS

while($startPoint = strpos($data, '<option>')){

    $data = substr($data, $startPoint);
    
    $pattern = '@([A-Z]{2})@';
    $state[$i] = get_words($pattern, $data);

    $endPoint2 = strpos($data, '</opt');
    $data = substr($data, $endPoint2 );

    $i++;
}

$i = 0;
while($i < sizeof($state)-49){ // LOOP THROUGH ALL STATES NAMES IN state ARRAY

    $post = [
        'city' => '',
        'state' => $state[$i],
        'submit' => 'submit',
    ];

    $i++;
    curl_setopt($handle, CURLOPT_POSTFIELDS, http_build_query($post));
    $data = curl_exec($handle);

    $proLink = array();
    $link = 0;

// HERE CODE FOR GETTING PROFILE LINKS

    // I CAN'T USE preg_math FOR THIS SITUATION. THAT'S WHY I USE preg_match_all
    $pattern = '@(\/.{11}\/\d{1,4})">View@';
    $num = preg_match_all($pattern, $data, $matches);

    while($link < $num){
        $proLink[$link] = $matches[1][$link]; //STORING ALL MATCHES
        $link++;
    }

    if(!$link){

        // SCRAPING PROFILE DATA
        foreach($proLink as $pro_link){
    
        $pro_url = 'http://examples.winautomation.com'.$pro_link;
        
       echo $pro_url."<br>";
    
        $per_data = curl_init($pro_url);
        curl_setopt($per_data, CURLOPT_RETURNTRANSFER, true); // Required for every new Links
      
        if($pageSource = curl_exec($per_data)){
          //  $pageSource = curl_exec($per_data);
            $startPoint = strpos($pageSource, 'name">');
            $pageSource = substr($pageSource, $startPoint);

            $pattern = '@>([a-zA-Z]+)@';
            $name = get_words($pattern, $pageSource);
            // echo $name. "\t\t";

            $startPoint = strpos($pageSource, '"surname');
            $pageSource = substr($pageSource, $startPoint);
            
            $pattern = '@">([a-zA-Z]+)@';
            $surname = get_words($pattern, $pageSource);
            //  echo $surname. "<br>";

            $startPoint = strpos($pageSource, 'ss">');
            $pageSource = substr($pageSource, $startPoint);
            
            $pattern = '@>([\d{2,4} a-zA-Z]+)@';
            $add = get_words($pattern, $pageSource);
            // echo $add. "<br>";

            $startPoint = strpos($pageSource, 'ty">');
            $pageSource = substr($pageSource, $startPoint);
            
            $pattern = '@>([a-zA-Z ]+)@';
            $city_ = get_words($pattern, $pageSource);
            // echo $city_. "<br>";

            $startPoint = strpos($pageSource, 'te">');
            $pageSource = substr($pageSource, $startPoint);
            
            $pattern = '@>([A-Z]{2})@';
            $state_ = get_words($pattern, $pageSource);
            // echo $state_. "<br>";
            
            $startPoint = strpos($pageSource, 'p">');
            $pageSource = substr($pageSource, $startPoint);
            
            $pattern = '@>(\d{3,5})@';
            $zip_ = get_words($pattern, $pageSource);
            // echo $zip_. "<br>";

            $startPoint = strpos($pageSource, 'try">');
            $pageSource = substr($pageSource, $startPoint);
            
            $pattern = '@>([A-Z]{2,3})@';
            $country_ = get_words($pattern, $pageSource);
            // echo $country_. "<br>";

            $startPoint = strpos($pageSource, 'on">');
            $pageSource = substr($pageSource, $startPoint);
            
            $pattern = '@>([a-zA-Z ]+)@';
            $occupation_ = get_words($pattern, $pageSource);
            // echo $occupation_. "<br>";
            
            $startPoint = strpos($pageSource, 'any"');
            $pageSource = substr($pageSource, $startPoint);
            
            $pattern = "@>([a-zA-Z' ]+)@";
            $company_ = get_words($pattern, $pageSource);
            // echo $company_. "<br><br>";
            
            $company_= mysqli_real_escape_string($conn, $company_);

            // $sql = "INSERT INTO personal (name, surname, address, city, state, zip_code, country, occupation, company) VALUES ('$name', '$surname', '$add', '$city_', '$state_', '$zip_', '$country_', '$occupation_', '$company_')";

            // if($conn->query($sql)){
            //     echo "OK<br>";
            // }else{
            //     echo $conn->error;
            // }

            } else{
                echo "Failed to Open ".$pro_url." Page<br>";
            }
        }
    }else{
        echo "There is no PROFILE LINK in this page. <br>";
    }
            
}

curl_close($handle);

function get_words($pattern, $string){

    preg_match($pattern, $string, $match);
    $data = $match[1];

    return $data;
}
