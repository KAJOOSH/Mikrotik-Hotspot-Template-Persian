<?php
use PEAR2\Net\RouterOS;
require_once 'Infrastructure/PEAR2/Autoload.php';

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
$SMSActive = $confing['SMSActive'];

$errors = array();

//Check if the form was submitted. Don't bother with the checks if not.
if (isset($_POST['act']))
{
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
        $printRequest->setQuery(RouterOS\Query::where('phone', $_POST['phone'])->orWhere('username', $_POST['phone'])->andWhere('comment', 'RegistrationFromApi'));
        $id = $client->sendSync($printRequest)->getProperty('.id');
        $id2 = $client->sendSync($printRequest)->getProperty('password');
        $id3 = $client->sendSync($printRequest)->getProperty('username');

        if (null === $id)
        {
            $errors[] = nl2br("حساب کاربری با شماره همراه / نام کاربری\n[ $phoneNO ] یافت نشد.");
        }
    }

    if (empty($errors))
    {

        
	if ($SMSActive=="True")
	{
		$sharh = nl2br("نام کاربری: [ $id3 ]\nرمز عبور: [ $id2 ]\n$SMSfooter");
        // turn off the WSDL cache
        ini_set("soap.wsdl_cache_enabled", "0");
        $sms_client = new SoapClient($SMSaddress, array(
            'encoding' => 'UTF-8'
        ));

        $parameters['username'] = $SMSusername;
        $parameters['password'] = $SMSpassword;
        $parameters['to'] = $phoneNO;
        $parameters['from'] = $SMSfrom;
        $parameters['text'] = str_replace("<br />", '', $sharh);
        $parameters['isflash'] = $SMSisflash;

        $sms_client->SendSimpleSMS2($parameters)->SendSimpleSMS2Result;

        //echo "<script>alert(\"The two password did not match.\");</script>";
        

        //header("Location: $AddressBack");
        $errors[] = "نتیجه درخواست به شماره همراه [ $phoneNO ] پیامک می گردد.";
	}
	else
	{
		$errors[] =nl2br("مشخصات حساب کاربری به شرح ذیل می باشد\nنام کاربری: [ $id3 ]\nرمز عبور: [ $id2 ]");
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

    <title>internet hotspot > Forgot Password</title>
    <meta name="description" content="internet hotspot > Forgot Password" />
    <style>
        .card-header {
            padding: .05rem 1.25rem;
        }
    </style>
</head>

<body>
    <div class="form-signin">
        <div class="text-center mb-2">
            <img class="mb-2" src="Infrastructure/img/Forgot.svg" alt="" width="100" height="100"> </img>
        </div>
        <div class="card">
            <div class="card-header" align="center" style="background-color: #0288D1; color: #ecf0f1; "></div>
            <div class="card-body">
                <form class="form" action="" method="post" name="login" onsubmit="return doLogin()">
                    <div class="MarginBottom1">
                        <input class="form-control" id="phone" name="phone" type="text" placeholder="شماره همراه یا نام کاربری" required autofocus />
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
