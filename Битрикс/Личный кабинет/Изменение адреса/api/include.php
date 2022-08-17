<?php

function getSdekCities($cityName)
{
    $clientId = 'DkQ06WKuhf30MTm61XE1eDHln9J6LcEF';
    $clientSecret = '1T7339tHKaC4nXQ7wej9eOnMR8ecRQwG';
    if (!(isset($clientId) and isset($clientSecret) and (($clientId."") != "") and (($clientSecret."") != ""))) {echo "Вы не заполнили \$clientId или \$clientSecret необходимые для получения авторизационного токена.";exit;}
    $curl1 = curl_init();//Инициализируем переменную CURL
    $postFieldsAr = array(//В 3-х строчках ниже данные которые передаются методом POST в первом запросе(то есть в запросе авторизационного токена), в втором запросе они не используются
        "grant_type" => "client_credentials",//Тип
        "client_id" => $clientId,//Идентификатор клиента
        "client_secret" => $clientSecret//Секретный ключ клиента
    );

    curl_setopt_array($curl1, array(
        CURLOPT_URL => "https://api.cdek.ru/v2/oauth/token?parameters",//URL на который отправляем запрос, в данном случаи это URL получения авторизационного токена
        CURLOPT_RETURNTRANSFER => true,//Устанавливаем этот параметр в true чтобы сохранить результат запроса в переменную иначе вывод сразу же будет направлен в браузер
        CURLOPT_POSTFIELDS => http_build_query($postFieldsAr)//Кодирование массива с оправляемыми данными для передачи методом POST
    ));

    $response = curl_exec($curl1);//Сохраняем тело ответа от сервера CDEK в переменную $response
    curl_close($curl1);
//[КОНЕЦ]Первый запрос(получение авторизационного токена)

//[НАЧАЛО]Декодируем полученный ответ из JSON проверяем наличие свойства access_token и если данное свойство не пустое присваиваем его переменной $authToken
    $objJSON1 = json_decode($response);
    if ($objJSON1 === false) {
        echo "Ошибка, сервер CDEK вернул ответ не в JSON формате.";exit;
    }

    if (isset($objJSON1->access_token) and (($objJSON1->access_token."") != "")) {
        $authToken = $objJSON1->access_token;
    } else {
        echo "Ошибка, ответ сервера CDEK не содержит свойства access_token или этот параметр пустой.";exit;
    }

    $curl2 = curl_init();//Инициализируем переменную вторую переменную CURL, хотя ничего не мешает использовать ту же самую, подход с двумя переменными используется для наглядного разделения двух запросов к серверу CDEK

    $header = array();//Создание массива в котором будут хранится заголовки отправляемые серверу CDEK
    $header[] = "Accept: application/json";//Этим заголовком можно указать серверу CDEK что в качестве ответа от него ожидается JSON строка, он закомментирован поскольку его наличие не обязательно
    $header[] = "Content-Type: application/json; charset=utf-8";//Этот заголовок указывает что данные передаваемые пост запросом имеют формат JSON строки и имеют кодировку UTF-8
    $header[] = "Authorization: Bearer " . $authToken;//Этот заголовок передаёт авторизационный токен, который был получен в первом запросе

    curl_setopt_array($curl2,array(
        CURLOPT_URL => 'https://api.cdek.ru/v2/location/cities?country_codes=RU&city='.$cityName,//URL на который отправляем запрос, в данном случаи это URL калькулятора расчёта доставки
        CURLOPT_RETURNTRANSFER => true,//Этот параметр установлен для того чтобы результат сохранялся в переменную а не сразу выводился в браузер
        CURLOPT_HTTPHEADER => $header,//Передаём массив заголовков подготовленный ранее, который содержит в частности заголовок с авторизационным токеном
    ));

    $response2 = curl_exec($curl2);
    curl_close($curl2);

    $arr = json_decode($response2);

    return $arr[0]->code;
}

function getSdekAvailableRates($from, $posID)
{
    $usefulDeliveryTypes = [136, 137, 233, 234, 482, 483];
    $clientId = 'DkQ06WKuhf30MTm61XE1eDHln9J6LcEF';
    $clientSecret = '1T7339tHKaC4nXQ7wej9eOnMR8ecRQwG';
    if (!(isset($clientId) and isset($clientSecret) and (($clientId."") != "") and (($clientSecret."") != ""))) {echo "Вы не заполнили \$clientId или \$clientSecret необходимые для получения авторизационного токена.";exit;}

// В строчке ниже объект содержащий запрос, этот объект необходимо передавать представленным в виде строки JSON, в строчке ниже переменной сразу же присваивается строка JSON
    $requestObjectString = '{
    "type": 1,
    "date": "'.date('Y-d-m').'T'.date('G:i:s').'+0700",
    "currency": 1,
    "lang": "rus",
    "from_location": {
        "code": '.$from.'
    },
    "to_location": {
        "code": '.$posID.'
    },
    "packages": [
        {
            "height": 10,
            "length": 10,
            "weight": 10,
            "width": 10
        }
    ]
}';
//[НАЧАЛО]Первый запрос(получение авторизационного токена)
    $curl1 = curl_init();//Инициализируем переменную CURL
    $postFieldsAr = array(//В 3-х строчках ниже данные которые передаются методом POST в первом запросе(то есть в запросе авторизационного токена), в втором запросе они не используются
        "grant_type" => "client_credentials",//Тип
        "client_id" => $clientId,//Идентификатор клиента
        "client_secret" => $clientSecret//Секретный ключ клиента
    );

    curl_setopt_array($curl1,array(
        CURLOPT_URL => "https://api.cdek.ru/v2/oauth/token?parameters",//URL на который отправляем запрос, в данном случаи это URL получения авторизационного токена
        CURLOPT_RETURNTRANSFER => true,//Устанавливаем этот параметр в true чтобы сохранить результат запроса в переменную иначе вывод сразу же будет направлен в браузер
        CURLOPT_POST => true,//Указываем что данные отправляются методом POST
        CURLOPT_POSTFIELDS => http_build_query($postFieldsAr)//Кодирование массива с оправляемыми данными для передачи методом POST
    ));

    $response = curl_exec($curl1);//Сохраняем тело ответа от сервера CDEK в переменную $response
    curl_close($curl1);

//[НАЧАЛО]Декодируем полученный ответ из JSON проверяем наличие свойства access_token и если данное свойство не пустое присваиваем его переменной $authToken
    $objJSON1 = json_decode($response);
    if ($objJSON1 === false) {
        echo "Ошибка, сервер CDEK вернул ответ не в JSON формате.";exit;
    }

    if (isset($objJSON1->access_token) and (($objJSON1->access_token."") != "")) {
        $authToken = $objJSON1->access_token;
    } else {
        echo "Ошибка, ответ сервера CDEK не содержит свойства access_token или этот параметр пустой.";exit;
    }
    $curl2 = curl_init();//Инициализируем переменную вторую переменную CURL, хотя ничего не мешает использовать ту же самую, подход с двумя переменными используется для наглядного разделения двух запросов к серверу CDEK

    $header = array();//Создание массива в котором будут хранится заголовки отправляемые серверу CDEK
    $header[] = "Accept: application/json";//Этим заголовком можно указать серверу CDEK что в качестве ответа от него ожидается JSON строка, он закомментирован поскольку его наличие не обязательно
    $header[] = "Content-Type: application/json; charset=utf-8";//Этот заголовок указывает что данные передаваемые пост запросом имеют формат JSON строки и имеют кодировку UTF-8
    $header[] = "Authorization: Bearer " . $authToken;//Этот заголовок передаёт авторизационный токен, который был получен в первом запросе

    curl_setopt_array($curl2,array(
        CURLOPT_URL => 'https://api.cdek.ru/v2/calculator/tarifflist',//URL на который отправляем запрос, в данном случаи это URL калькулятора расчёта доставки
        CURLOPT_RETURNTRANSFER => true,//Этот параметр установлен для того чтобы результат сохранялся в переменную а не сразу выводился в браузер
        CURLOPT_HTTPHEADER => $header,//Передаём массив заголовков подготовленный ранее, который содержит в частности заголовок с авторизационным токеном
        CURLOPT_POST => true,//Указываем что данные отправляются методом POST
        CURLOPT_POSTFIELDS => $requestObjectString//Передача в качестве POST данных подготовленный ранее объект в виде строки JSON
    ));

    $response2 = curl_exec($curl2);//Сохраняем тело ответа второго запроса от сервера CDEK в переменную $response2
    curl_close($curl2);

    $arr = json_decode($response2)->tariff_codes;

    for ( $i = 0; $i < count($arr); $i++ )
    {
        if(in_array($arr[$i]->tariff_code, $usefulDeliveryTypes)) $availableDeliveries[] = $arr[$i]->tariff_code;
    }

    return $availableDeliveries;
}

function getSdekResults($cityID)
{
    return $availableDeliveries = getSdekAvailableRates(44, $cityID);
}

















