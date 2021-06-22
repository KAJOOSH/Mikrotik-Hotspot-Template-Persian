<h1 align="center">قالب هات اسپات میکروتیک (فارسی)</h1>
<h3 align="center">Mikrotik Hotspot Template Persian</h3>

<div dir="rtl">
 
## نحوه ی نصب

آخرین نسخه را دانلود کرده و لذت ببرید


## راه اندازی قالب Hotspot Mikrotik 


1- فایل html.login در پوشه HTML (Special Mikrotik) را با نرم افزار Notepad باز نمائید. سپس جهت اصالح لینک "رمز عبور را فراموش 
کرده اید؟" و "حساب کاربر ی ندار ید؟ اکنون ایجاد کنید" لینک وب سایت خود را جایگزین "8080:localhost://http " 
نمائید.

 2- فایل html.status در پوشه HTML (Special Mikrotik) را با نرم افزار Notepad باز نمائید. سپس جهت اصالح لینک "مدیریت کاربری" 
پورت HTTP میکروتیک خود را جایگزین "8081):hostname://$(http " نمائید.
 
  پس از فعال سازی Hotspot امکان دسترسی به userman به دلیل اشغال بود پورت 80 میسر نمی باشد و 
باید پورت HTTP را تغییر دهید، به طور مثال:

</div>

```bash
 /ip service set www port=8081
```
 
 <div dir="rtl">
 
## فعال سازی Signup در Userman Mikrotik
 
1- به بخش تنظیمات userman وارد شده و Signup فعال نمائید. (allowed Signup < Signup < Settings)

2- انتقال پوشه umfiles به میکروتیک 

 ## راه اندازی صفحه های ایجاد کاربری، تغییر رمز عبور و بازیابی رمز عبور
 
 1- پوشه PHP (Special Host) را به وب سایت خود منتقل نمائید.
 
 2- فایل config.php در مسیر Infrastructure باز نمائید و تنظیمات لازم را انجام دهید.
</div>

```bash
'SMSActive' => 'False',                       // فعال یا غیر فعال سازی ارسال پیامک
'SMSaddress' => '',                           // آدرس وب سرویس پیامک
'SMSusername' => '',                          // نام کاربر ی پنل پیامک
'SMSpassword' => '',                          // رمز عبور پنل پیامک 
'SMSfrom' => '',                              // شماره ارسال کننده پیامک
'SMSisflash' => false,                        // نوع ارسال: به صورت فلش و یا عادی
'SMSfooter' => '',                            // متن پایین پیامک
'MIKROTIKip' => '192.168.88.1',               // آی پی / آدرس میکروتیک
'MIKROTIKusername' => 'admin',                // نام کاربر ی میکروتیک
'MIKROTIKpassword' => '',                     // رمز عبور میکروتیک 
'USERMANcustomer' => 'admin',                 // میکروتیک userman نام کاربر ی 
'USERMANsharedUsers' => '1',                  // حداکثر تعداد همزمان اتصال
'USERMANprofile' => 'TEST',                   // پروفایل پیش فرض بعد از ایجاد کاربری
'AddressBack' => 'http://192.168.88.1/'       // آدرس بازگشت
```

<h3 align="left">Languages and Tools:</h3>
<p align="left"> <a href="https://getbootstrap.com" target="_blank"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/bootstrap/bootstrap-plain-wordmark.svg" alt="bootstrap" width="40" height="40"/> </a> <a href="https://www.w3schools.com/css/" target="_blank"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/css3/css3-original-wordmark.svg" alt="css3" width="40" height="40"/> </a> <a href="https://www.w3.org/html/" target="_blank"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/html5/html5-original-wordmark.svg" alt="html5" width="40" height="40"/> </a> <a href="https://developer.mozilla.org/en-US/docs/Web/JavaScript" target="_blank"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/javascript/javascript-original.svg" alt="javascript" width="40" height="40"/> </a> <a href="https://www.php.net" target="_blank"> <img src="https://raw.githubusercontent.com/devicons/devicon/master/icons/php/php-original.svg" alt="php" width="40" height="40"/> </a> </p>
