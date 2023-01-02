<?php
/*
 * пример простой формы для быстрого старта
 */

//Введите ваш token
$api_xdrive = new apiXDRIVE('{YOUR_TOKEN}');
//Введите регистрационный номер лидера под которого будет производиться регистрация новичка
$sponsor_id = 1111111111;
//На эту страницу будет происходить редирект (См. ограничения в описании API)
$return_url = 'https://faberlic.com/index.php?option=com_user&view=cabinet&Itemid=2069&lang=ru';

$post_action = isset($_POST['action']) ? strip_tags($_POST['action']) : '';
if(!empty($post_action)) {

    $result = '';

    //Send code SMS - ajax
    if($post_action == 'sms') {
        //Номер телефона
        $phone = isset($_POST['phone']) ? strip_tags($_POST['phone']) : 0;
        //Комментарий
        $comment = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $sponsor_id;
        //Отправка запроса
        $send_sms = $api_xdrive->sendSMS($phone, $comment);
        if(isset($send_sms['response']['status']) && $send_sms['response']['status'] == 'success') {
            //Example: {"response":{"method":"sms","status":"success","count available sms":9,"daily_sms_limit":10}}
	        $result = '<span style="color:green">Verification code sent to phone number '.$phone.'</span>';
        } else {
            //Example: {"error":{"code":"902","text":"SMS limit exceeded"}}
	        $result = json_encode($send_sms);
        }
    }

    //Add Lead - POST
    elseif ($post_action == 'add') {
        $post_data = array();
        foreach ($_POST as $key=>$value) {
            $post_data[$key] = strip_tags($value);
        }
        $post_data['return_url'] = $return_url;
        $addLead = $api_xdrive->addLead($post_data);
        if(isset($addLead['addLead'])) {

            /*
             * Возможность регистрации не верифицированного новичка
             * даётся только с согласования менеджером проекта
             */
            if(!empty($addLead['addLead']['redirect_url'])) {
                //Verified user
	            header("Location: " . $addLead['addLead']['redirect_url']);
            } else {
                //unVerified user
	            header("Location: " . $return_url);
            }

        } else {
            if(is_array($addLead)) {
	            $result = json_encode($addLead);
            } else {
	            $result = $addLead;
            }

        }


    }
    else {
	    $result = 'Error action';
    }

    echo $result;
    exit();
}


//Error_list
$error_array = $api_xdrive->errorList();
$errors = '<br><br><br><h3>Errors list</h3><table class="table">';
foreach ($error_array as $key=>$value) {
	$errors .= '<tr><td><b>'.$key.'</b></td><td>'.$value.'</td></tr>';
}
$errors .= '</table>';


if(isset($_GET['dev'])) {
    //user.checkPhoneInFaberlic
    $phone = 79969341304;
	$indo = $api_xdrive->checkPhoneInFaberlic($phone);
    var_dump($indo);
}

?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
	<title>ApiXDRIVE simple form</title>
    <script
            src="https://code.jquery.com/jquery-3.6.1.min.js"
            integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ="
            crossorigin="anonymous"></script>
    <style>
        html {
            font-size: 16px;
        }
        body {
            padding: 5rem 1rem;
            margin: 0;
            background: #eee;
        }
        .box {
            padding: 2rem;
            border-radius: 1rem;
            box-shadow: 0.5rem 0.5rem 1rem rgba(0,0,0,0.1);
            background: #fff;
        }
        .sms_btn {
            color: blue;
            margin-top: 0.5rem;
            text-decoration: underline;
            cursor: pointer;
        }
        .sms_btn:hover {
            opacity: 0.8;
        }
        .sms_result {
            color: red;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>

<div class="row">
    <div class="col-12 col-sm-4 offset-sm-4">
        <div class="box">
            <form method="post">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="sponsor_id" value="<?=$sponsor_id?>">
                <h2>Fast registration form</h2>
                <div class="mb-3">
                    <label class="form-label">Surname</label>
                    <input type="text" class="form-control" name="surname" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">Patronymic</label>
                    <input type="text" class="form-control" name="patronymic" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">Birthday</label>
                    <input type="text" class="form-control" name="birthday" value="" placeholder="dd.mm.yyyy">
                </div>
                <div class="mb-3">
                    <label class="form-label">Phone</label>
                    <input type="text" class="form-control" name="phone" value="" placeholder="+79998887766">
                    <div class="sms_form">
                        <div class="sms_btn">Get verification code</div>
                        <div class="sms_result" id="sms_result"></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="text" class="form-control" name="email" value="">
                </div>
                <div class="mb-3">
                    <label class="form-label">Sex</label>
                    <select class="form-control" name="sex" aria-label="Default select example">
                        <option value="f">female</option>
                        <option value="m">male</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Country</label>
                    <select class="form-control" name="country"  aria-label="Default select example">
                        <option value="ru">Russia</option>
                        <option value="am">Armenia</option>
                        <option value="by">Belarus</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Sms confirmation code</label>
                    <input type="text" class="form-control" name="code" value="">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
            </form>

            <?=$errors?>
        </div>
    </div>
</div>
<script>

    function loadAjaxArr(arr, idx_result) {
        jQuery.ajax({
            url:      '/',
            type:     "POST",
            dataType: "html",
            data: arr,
            success: function(response) {
                $('#' + idx_result).html(response);
            },
            error: function(response) {
                $('#' + idx_result).html('Error request');
            }
        });
    }

    $(document).on('click','.sms_btn',function(){

        let user_phone = $('input[name=phone]').val(),
            post_arr = [];
        if(user_phone.length > 0) {
            post_arr = {
                action:'sms',
                phone:user_phone
            }
            loadAjaxArr(post_arr, 'sms_result');

        } else {
            $('#sms_result').html('Error request');
        }

    })

</script>
</body>
</html>
