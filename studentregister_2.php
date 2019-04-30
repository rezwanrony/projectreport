<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header('Access-Control-Allow-Methods: POST');


$db=mysqli_connect('localhost','datalibrary','Cis@360#','datalibr_roni');


if($_SERVER['REQUEST_METHOD'] == "POST"){
    // Get data


    $data = json_decode(file_get_contents("php://input"));

    //SMTP needs accurate times, and the PHP time zone MUST be set
    //This should be done in your php.ini, but this is how to do it if you don't have access to that
    date_default_timezone_set('Etc/UTC');
    $email= 'iamrezwanrony@gmail.com';
    $code= mt_rand(100000,999999);
    //$code = 123456;


    $fquery = mysqli_query($db,"SELECT * FROM student WHERE email LIKE '%$data->email%'");

    $result = mysqli_num_rows($fquery);
    /*  echo json_encode($result);
    exit();*/
    $key = '@diu.edu.bd';
    $condition = (strpos($data->email, $key) == true);
   /* echo json_encode($condition);
    exit();*/
    if (strpos($data->email, $key) == true) {



        if($result > 0) { //check if there is already an entry for that username
            $json = array("status" => 0, "msg" => "email already exist!!");
        }


        else{



            $to = $data->email; // note the comma
            // Subject
            $subject = 'Verification Code';
            // Message
            $message = "Thanks for signing up! 
                        Your account has been created, you can verify your account with the following verification code.</br>
                        Please use this verification code to activate your account.
                        Verification code=$code";

            $headers[] = 'MIME-Version: 1.0';
            $headers[] = 'Content-type: text/html; charset=iso-8859-1';
            // Additional headers
            $headers[] = 'From:rezwan35-982@diu.edu.bd';
            //End Mail Part
            // Insert data into data base
            $query = mysqli_query($db, "INSERT INTO student (name, email, password, phone ,address, code) VALUES ('$data->name','$data->email','$data->password','$data->phone','$data->address', $code)");

            if($query){

                $mail_sent = mail($to, $subject, $message, implode("\r\n", $headers));

                if(!$mail_sent)
                {
                    $json = array("status" => 0, "msg" => "mail sent error");
                }
                else
                {
                    $json = array("status" => 1, "msg" => "Successfully Registered, a verification code is sent to your email");

                }

            }else{
                $json = array("status" => 0, "msg" => "Error adding user!");
            }
        }

    }

    else{

        $json = array("status" => 0, "msg" => "The email must be a registered diu email");
    }

}

else{
    $json = array("status" => 0, "msg" => "Request method not accepted");
}


mysqli_close($db);

/* Output header */
header('Content-type: application/json');
echo json_encode($json);



?>