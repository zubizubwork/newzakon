<?

//добавление в корзину товаров

include("db.php");
include("f_data.php");
include("shifr_pass.php");
include("send_sms.php");

$type = f_data ($_POST['type'], 'text', 0);
$phone = f_data ($_POST['phone'], 'text', 0);
$phone = clearPhone($phone);
$text = f_data ($_POST['text'], 'text', 0);
$date = date("H:i d.m.Y");

//первый этап
if ($type==1)
{
    $result = mysql_query("SELECT * FROM users WHERE phone='$phone' ORDER BY id DESC");
    $myrow = mysql_fetch_assoc($result); 
    $num_rows = mysql_num_rows($result);

    if ($num_rows!=0)
    {
        echo "0";
        exit;
    }
    else
    {
        $kod = md5(date("d.m.Y")."евро".time()."ffga");
        $kod = substr($kod,5,5);
        $text = "Для подтверждения регистрации введите код: $kod";
        sendSms($phone,$text);
        echo $kod;
    } 
}


//второй этап
if ($type==2)
{
    /*
    //если пользователь авторизован, то тел равен 777
    if ($phone!="777")
    {
       
    }
    else
    {
        $result = mysql_query("SELECT * FROM users WHERE uid='$_COOKIE[uid]' ORDER BY id DESC");
    }
    */
    
    
    $result = mysql_query("SELECT * FROM users WHERE phone='$phone' ORDER BY id DESC");
    $myrow = mysql_fetch_assoc($result); 
    $num_rows = mysql_num_rows($result);

    if ($num_rows!=0 && $phone!='777')
    {
        echo "0";
        exit;
    }
    else
    {
        if ($phone!="777")
        {
            $kod = md5(date("d.m.Y")."евро".time()."ffga");
            $uid = md5($kod.$phone.date("d.m.Y").time());
            $u_pass = generate_password(7);
            $pass = md5(md5($u_pass));
            $date = date("d.m.Y");
            $real_pass = creat_pass($u_pass);

            $result_add = mysql_query ("INSERT INTO users (uid,name,pass,phone,mail,fio,date_reg,status,text,real_pass,podtverjdenie,mail_enabl2) 
            VALUES ('$uid','$phone','$pass','$phone','','Пользователь','$date','Пользователь','-','$real_pass','1','1')");
            
            $result_add = mysql_query ("INSERT INTO zadaniy (name,type,text,uid,date) VALUES ('Новое задание','-','$text','$uid','$date')");
            
            $text = "Регистрация на сайте $_SERVER[HTTP_HOST] завершена! Ваш логин: $phone, Ваш пароль: $u_pass";
            sendSms($phone,$text);
            echo $kod;
        }
        else
        {
            $uid = $_COOKIE[uid];
            $result_add = mysql_query ("INSERT INTO zadaniy (name,type,text,uid,date) VALUES ('Новое задание','-','$text','$uid','$date')");
            echo "OkZadanie";
        } 
    } 
}

?>