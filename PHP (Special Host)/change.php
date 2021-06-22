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

    if (empty($_POST['email']))
    {
        $errors[] = 'Email is required.';
    }

    if (empty($_POST['phone']))
    {
        $errors[] = 'Phone is required.';
    }

    if (empty($errors))
    {
        //Check if this is an imposter or not
        $printRequest = new RouterOS\Request('/tool user-man user print .proplist=.id,phone,username');
        $printRequest->setQuery(RouterOS\Query::where('phone', $_POST['phone'])->orWhere('username', $_POST['phone'])->andWhere('password', $_POST['email'])->andWhere('comment', 'RegistrationFromApi'));
        $id = $client->sendSync($printRequest)->getProperty('.id');
        $id2 = $client->sendSync($printRequest)->getProperty('phone');
        $id3 = $client->sendSync($printRequest)->getProperty('username');

        if (null === $id)
        {
            $errors[] = 'شماره همراه / نام کاربری و یا رمز عبور فعلی نادرست است.';
        }
    }

    if (!isset($_POST['password']) || !isset($_POST['password2']))
    {
        $errors[] = 'رمز عبور جدید وارد نشده است';
    }

    if (empty($errors))
    {
        if ($_POST['password'] !== $_POST['password2'])
        {
            $errors[] = 'رمز عبور و تکرار آن یکسان نمی باشد.';
        }
        else
        {
            //Here's the fun part - actually changing the password
            $setRequest = new RouterOS\Request('/tool user-man user set');
            $client->sendSync($setRequest->setArgument('password', $_POST['password'])->setArgument('numbers', $id));

            $id4 = $_POST['password'];

            $sharh = nl2br("نام کاربری: [ $id3 ]\nرمز عبور: [ $id4 ]\n$SMSfooter");
		if ($SMSActive=="True")
		{
            // turn off the WSDL cache
            ini_set("soap.wsdl_cache_enabled", "0");
            $sms_client = new SoapClient($SMSaddress, array(
                'encoding' => 'UTF-8'
            ));

            $parameters['username'] = $SMSusername;
            $parameters['password'] = $SMSpassword;
            $parameters['to'] = $id2;
            $parameters['from'] = $SMSfrom;
            $parameters['text'] = str_replace("<br />", '', $sharh);
            $parameters['isflash'] = $SMSisflash;

            $sms_client->SendSimpleSMS2($parameters)->SendSimpleSMS2Result;

            //Redirect back to the login page, thus indicating success.
            //$errors[] = 'باموفقیت تغییر کرد';
            //header("Location: $AddressBack");
            $errors[] = "نتیجه درخواست به شماره همراه [ $id2 ] پیامک می گردد.";
		}
		else
		{
			$errors[] ="مشخصات حساب کاربری با موفقیت تغییر یافت";
		}
		
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

    <title>internet hotspot > Change password</title>
    <meta name="description" content="internet hotspot > Change password" />
    <style>
        .card-header {
            padding: .05rem 1.25rem;
        }
    </style>
</head>

<body>
    <div class="form-signin">
        <div class="text-center mb-2">
            <img class="mb-2" src="Infrastructure/img/change.svg" alt="" width="100" height="100"> </img>
        </div>
        <div class="card">
            <div class="card-header" align="center" style="background-color: #0288D1; color: #ecf0f1; "></div>
            <div class="card-body">
                <form class="form" action="" method="post" name="login" onsubmit="return doLogin()">
                    <div class="MarginBottom1">
                        <input class="form-control"  id="phone" name="phone" type="text" placeholder="شماره همراه یا نام کاربری" required autofocus />
                    </div>
                    <div class="MarginBottom1">
                        <input class="form-control" id="email" name="email" type="password" placeholder="رمز عبور فعلی" required autofocus />
                    </div>
                    <div class="MarginBottom1">
                        <input class="form-control" id="password" name="password" type="password" placeholder="رمز عبور جدید" required autofocus />
                    </div>
                    <div class="MarginBottom1">
                        <input class="form-control" id="password2" name="password2" type="password" placeholder="تکرار رمز عبور جدید" required autofocus />
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
