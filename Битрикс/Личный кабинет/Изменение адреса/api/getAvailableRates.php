<?php
function getAvailableRates()
{
    global $USER;
    $idUser = $USER->GetID();
    $rsUser = CUser::GetByID($idUser);
    $arUser = $rsUser->Fetch();
    $usefulDeliveryTypes = $arUser['UF_TARIFFS_SDEK_CODE'];

    $path = $_SERVER['DOCUMENT_ROOT'].'/rates.json';
    $json = realpath($path);
    $array = file_get_contents($json);
    if (!$array || $array)
    {
        $path = $_SERVER['DOCUMENT_ROOT'].'/ratesSecond.json';
        $json = realpath($path);
        $array = file_get_contents($json);
    }

        $arr = json_decode($array)->tariff_codes;

        for ( $i = 0; $i < count($arr); $i++ )
        {
            if(in_array($arr[$i]->tariff_code, $usefulDeliveryTypes)) $availableDeliveries[] = $arr[$i];
        }

    return $availableDeliveries;

}
?>