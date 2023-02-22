<?php
/*
 * пример простой формы для быстрого старта
 */

//Подключение класса
require_once $_SERVER['DOCUMENT_ROOT'] . '/classes/apiXDRIVE.class.php';
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
        $fio_arr = isset($post_data['fio']) ? explode(' ', $post_data['fio']) : array();

        unset($post_data['fio']);
        $post_data['surname'] = isset($fio_arr[0]) ? $fio_arr[0] : '';
        $post_data['name'] = isset($fio_arr[1]) ? $fio_arr[1] : '';
        $post_data['patronymic'] = isset($fio_arr[2]) ? $fio_arr[2] : '';

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

/*
 * Отправка уведомления в телеграм владельца токена
$message = 'Hello';
$tg = $api_xdrive->sendMessageTelegram($message);
var_dump($tg['response'][0]['ok']);
*/
?>

<!doctype html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link type="text/css" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
	<title>ApiXDRIVE simple form</title>
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <script
            src="https://code.jquery.com/jquery-3.6.1.min.js"
            integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ="
            crossorigin="anonymous"></script>

    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<div class="page_wrapper">
    <header>
        <div class="logo"><img src="img/logo_faberlic.svg" alt="logotype Faberlic" title="logotype Faberlic"> </div>
    </header>
    <nav>
        <ul>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/1?q=%3Arelevance%3AperiodShields%3Anew&sponsornumber=<?=$sponsor_id?>">Новинки</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/cosmetics?sponsornumber=<?=$sponsor_id?>">Косметика</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/perfumery?sponsornumber=<?=$sponsor_id?>">Парюмерия</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/clothes-and-accessories?sponsornumber=<?=$sponsor_id?>">Одежда и аксессуары</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/health?sponsornumber=<?=$sponsor_id?>">Здоровье</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/everything-for-home?sponsornumber=<?=$sponsor_id?>">Дом</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/goods-for-kids?sponsornumber=<?=$sponsor_id?>">Детям</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/for-men?sponsornumber=sponsornumber=<?=$sponsor_id?>">Мужчинам</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/for-business?sponsornumber=<?=$sponsor_id?>">Для бизнеса</a></li>
            <li><a target="_blank" href="https://new.faberlic.com/ru/c/1?q=%3Arelevance%3AperiodShields%3Apromo&sponsornumber=<?=$sponsor_id?>"
                   class="color_red">AKL</a></li>
        </ul>
    </nav>

    <h2>Регистрируйся и покупай со <span class="color_red">скидкой 20%</span></h2>

    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="offer_action">
                <img src="img/action.jpeg" alt="Акция новичка Faberlic" title="Акция новичка Faberlic">
            </div>
        </div>
        <div class="col-12 col-sm-6">
            <div class="box">

                <form method="post">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="sponsor_id" value="<?=$sponsor_id?>">
                    <input type="hidden" name="sex" value="f"><!-- f - женский пол, m - мужской пол -->
                    <div class="mb-3">
                        <label class="form-label" for="input_fio">
                            <span class="input_name">Ваше ФИО</span>
                            <input type="text" class="form-control" name="fio" value="" id="input_fio" data-required="1">
                            <span class="error">Заполните обязательное поле</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="input_name">Дата рождения</span>
                            <input type="text" class="form-date form-control" name="birthday" value="" placeholder="dd.mm.yyyy" data-required="1">
                            <span class="error">Заполните обязательное поле</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="country">Страна проживания</label>
                        <select class="form-control" name="country" id="country">
                            <option value="ru">Россия</option>
                            <option value="kz">Казахстан</option>
                            <option value="by">Беларусь</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <span class="input_name">Мобильный телефон</span>
                        <div class="block_phone">
                            <label class="form-label">
                                <input type="text" class="form-phone form-control" name="phone" value="" placeholder="+79998887766" data-required="1">
                                <span class="error">Заполните обязательное поле</span>
                            </label>
                            <div class="sms_form">
                                <div class="sms_btn">Получить SMS<span>-код</span></div>
                            </div>
                        </div>
                        <div class="sms_result" id="sms_result"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="input_name">E-mail</span>
                            <input type="text" class="form-control" name="email" value="" data-required="1">
                            <span class="error">Заполните обязательное поле</span>
                        </label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            <span class="input_name">Введите код из SMS</span>
                            <input type="text" class="form-code form-control" name="code" value="" data-required="1">
                            <span class="error">Заполните обязательное поле</span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn_check_required">Отправить и получить скидку до 20%</button>
                    <div class="btn_desc">
                        <p>Согласие на обработку персональных данных.</p>
                        <p>Нажимая кнопку «Отправить и получить скидку до 20%» Вы подтверждаете, что даете согласие на обработку своих персональных данных.</p>
                        <p>Регистрируясь Вы соглашаетесь с условиями соглашения.</p>
                        <p>Нажимая на кнопку «Подать заявку», я соглашаюсь с условиями Публичной оферты.</p>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <footer>
        Сайт лидера №<?=$sponsor_id?> Иванова Иван Ивановича, +79998887766, ivan@ivanovix.ru. Copyright © 2023
    </footer>
</div>


<script src="js/jquery.inputmask.bundle.min.js" type="text/javascript"></script>
<script src="js/script.js" type="text/javascript"></script>
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
            $('#sms_result').html('Необходимо заполнить номер телефона');
        }

    })

</script>
</body>
</html>
