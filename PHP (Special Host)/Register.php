<?php

function RandomPass($length = 8)
{
    //  $vmsString = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789,./"[]{}!@#$%^&*()=-';
    $vmsString = '0123456789';
    return substr(str_shuffle($vmsString) , 0, $length);
}
function RandomUser($length = 8)
{
	$vmsString = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    return substr(str_shuffle($vmsString) , 0, $length);
}

use PEAR2\Net\RouterOS;
require_once 'Infrastructure/PEAR2/Autoload.php';

require ('Infrastructure/routeros_api.class.php');
$confing = include ('Infrastructure/config.php');

$SMSaddress = $confing['SMSaddress'];
$SMSusername = $confing['SMSusername'];
$SMSpassword = $confing['SMSpassword'];
$SMSfrom = $confing['SMSfrom'];
$SMSisflash = $confing['SMSisflash'];
$SMSfooter = $confing['SMSfooter'];
$MIKROTIKip = $confing['MIKROTIKip'];
$MIKROTIKusername = $confing['MIKROTIKusername'];
$MIKROTIKpassword = $confing['MIKROTIKpassword'];
$AddressBack = $confing['AddressBack'];
$USERMANcustomer = $confing['USERMANcustomer'];
$USERMANsharedUsers = $confing['USERMANsharedUsers'];
$USERMANprofile = $confing['USERMANprofile'];
$SMSActive = $confing['SMSActive'];
// *************************************************************************
$errors = array();

//Check if the form was submitted. Don't bother with the checks if not.
if (isset($_POST['act']))
{

    $fname = $_POST["fname"];
    $lname = $_POST["lname"];
    $phone = $_POST["phone"];
    $Npass = RandomPass(5);
	$NUserman=(RandomUser(2) . RandomPass(2) . '-' . substr($phone,7,10));
    try
    {
        //Adjust RouterOS IP, username and password accordingly.
        $client = new RouterOS\Client($MIKROTIKip, $MIKROTIKusername, $MIKROTIKpassword);
    }
    catch(Exception $e)
    {
        $errors[] = $e->getMessage();
    }

    if (empty($_POST['phone']))
    {
        $errors[] = 'شماره همراه وارد نشده است';

    }
    else
    {
        $phoneNO = $_POST['phone'];

    }

    if (empty($errors))
    {
        //Check if this is an imposter or not
        $printRequest = new RouterOS\Request('/tool user-man user print .proplist=.id,username,password');
        $printRequest->setQuery(RouterOS\Query::where('comment', 'RegistrationFromApi')->andWhere('phone', $_POST['phone']));
        $id = $client->sendSync($printRequest)->getProperty('.id');

        if (null != $id)
        {
            $errors[] = "جهت شماره همراه [ $phoneNO ] حساب کاربری ایجاد شده است";
			

        }
    }

    if (empty($errors))
    {

        // *************************************************************************
        // new object api in routeros_api.class.php
		/*
        $API = new routerosapi();
        $API->debug = false;
        if ($API->connect($MIKROTIKip, $MIKROTIKusername, $MIKROTIKpassword))
        {

            $response = $API->comm("/tool/user-manager/user/add", array(
                "customer" => $USERMANcustomer,
                "username" => $phone, // substr($phone,7,10)
                "password" => $Npass,
				"phone" => $phone,
				"first-name" => $fname,
                "last-name" => $lname,
				"shared-users" => $USERMANsharedUsers
				
            ));

            $API->disconnect();

        }
			
		*/
	
	
    $addRequest = new RouterOS\Request('/tool/user-manager/user/add');
    $addRequest
        ->setArgument('customer', $USERMANcustomer)
        ->setArgument('username', $NUserman )
        ->setArgument('password', $Npass)
		->setArgument('phone', $phone)
		->setArgument('first-name', $fname)
		->setArgument('last-name', $lname)
		->setArgument('comment', 'RegistrationFromApi')
		->setArgument('shared-users', $USERMANsharedUsers);
		
    $newUser = $client->sendSync($addRequest);

    $activateRequest = new RouterOS\Request('/tool/user-manager/user/create-and-activate-profile');
    $activateRequest
        ->setArgument('customer', $USERMANcustomer)
        ->setArgument('numbers', $NUserman)
        ->setArgument('profile', $USERMANprofile);
    $add_user_profile = $client->sendSync($activateRequest);
	
	
        $sharh = nl2br("نام کاربری: [ $NUserman ]\nرمز عبور: [ $Npass ]\n$SMSfooter");

		if ($SMSActive=="True")
		{
			// turn off the WSDL cache
			ini_set("soap.wsdl_cache_enabled", "0");
			$sms_client = new SoapClient($SMSaddress, array(
				'encoding' => 'UTF-8'
			));

			$parameters['username'] = $SMSusername;
			$parameters['password'] = $SMSpassword;
			$parameters['to'] = $phone;
			$parameters['from'] = $SMSfrom;
			$parameters['text'] = str_replace("<br />", '', $sharh);
			$parameters['isflash'] = $SMSisflash;

			$sms_client->SendSimpleSMS2($parameters)->SendSimpleSMS2Result;

			$errors[] = "نتیجه درخواست به شماره همراه [ $phoneNO ] پیامک می گردد.";
		} else
		{
			$errors[] = nl2br("حساب کاربری شما با مشخصات ذیل ایجاد گردید\nنام کاربری: [ $NUserman ]\nرمز عبور: [ $Npass ]");
		}
    }
}
?>

<!DOCTYPE html>
<html dir="rtl">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta http-equiv="pragma" content="no-cache" />
    <meta http-equiv="expires" content="-1" />
    <link rel="icon" href="Infrastructure/img/favicon.ico" type="image/x-icon" />

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="Infrastructure/css/bootstrap.min.css" />
    <link rel="stylesheet" href="Infrastructure/css/floating-labels.css" />

    <title>internet hotspot > User Signup</title>
    <meta name="description" content="internet hotspot > User Signup" />
    <style>
        .card-header {
            padding: .05rem 1.25rem;
        }
    </style>
</head>

<body>
    <div class="form-signin">
        <div class="text-center mb-2">
            <img class="mb-2" src="Infrastructure/img/Register.svg" alt="" width="100" height="100"> </img>
        </div>
        <div class="card">
            <div class="card-header" align="center" style="background-color: #0288D1; color: #ecf0f1; "></div>
            <div class="card-body">
                <form class="form" action="" method="post" name="login" onsubmit="return doLogin()">
                    <div class="MarginBottom1">
                        <input class="form-control" pattern="[0-9]{11}" id="phone" name="phone" type="text" placeholder="شماره همراه" required autofocus />
                    </div>
                    <div class="MarginBottom1">
                        <input class="form-control" id="fname" name="fname" type="text" placeholder="نام" required autofocus />
                    </div>
                    <div class="MarginBottom1">
                        <input class="form-control" id="lname" name="lname" type="text" placeholder="نام خانوادگی" required autofocus />
                    </div>
                    <div class="container">
                        <div class="row justify-content-end">
                            <div class="col-0">
                                <button id="act" name="act" tabindex="0" class="btn btn-primary btn-block" type="submit">ارسال درخواست</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card-footer text-muted" align="right">
                <p class="card-text mb-0"><small class="text-muted"><a href="Forgot.php">رمز عبور را فراموش کرده اید؟</a></small></p>
                <p class="card-text mb-0"><small class="text-muted"><a href="<?php echo $AddressBack ?>">حساب کاربری دارید؟ اکنون وارد شوید</a></small></p>
            </div>
        </div>
        <div align="center">
            <br>
            <?php if(!empty($errors)) { ?>
                <div class="alert alert-info alert-dismissible fade show" role="alert" style="font-size:14px;">
                    <?php foreach ($errors as $error) { ?>
                        <?php echo $error; ?>
                            <?php } ?>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                </div>
                <?php } ?>
        </div>
        <p class="mt-1 mb-5 text-muted text-center">KAJOOSH.IR &copy;</p>
    </div>

    <script src="Infrastructure/js/jquery-3.3.1.slim.min.js"></script>
    <script src="Infrastructure/js/popper.min.js"></script>
    <script src="Infrastructure/js/bootstrap.min.js"></script>
    <script src="Infrastructure/js/jquery.responsiveTabs.js" type="text/javascript"></script>
    <!-- document.login.username.focus(); //-->
</body>

</html>