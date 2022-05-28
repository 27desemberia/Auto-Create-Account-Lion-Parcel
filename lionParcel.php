<?php

require_once 'scraping.php';

class LionParcel
{

    public function checkNomor($nomor)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://algo-api.lionparcel.com/v1/account/auth/customer/username/check?phone_number=0$nomor",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "GET",
        CURLOPT_POSTFIELDS => "",
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $json = json_decode($response, true);
        return $json;
    }

    public function getOtp($nomor)
    {
        $curl = curl_init();
        curl_setopt_array($curl, [
        CURLOPT_URL => "https://algo-api.lionparcel.com/v2/account/auth/otp/request",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => "{\n\"messaging_type\":\"SMS\",\n\t\"otp_type\":\"REGISTER\",\n\t\"phone_number\":\"+62$nomor\",\n\t\"role\":\"CUSTOMER\"\n}",
        CURLOPT_HTTPHEADER => [
            "Content-Type: application/json"
        ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $json = json_decode($response, true);
        return $json;
    }

    public function validateOtp($otpId, $otp)
    {
        $curl = curl_init();
            $post = [
                "otp_id" => (int)$otpId,
                "otp" => (int)$otp
            ];
            curl_setopt_array($curl, [
            CURLOPT_URL => "https://algo-api.lionparcel.com/v1/account/auth/otp/exchange",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $post,
            CURLOPT_HTTPHEADER => [
                "Content-Type: multipart/form-data",
                "content-type: multipart/form-data; boundary=---011000010111000001101001"
            ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            $json = json_decode($response, true);
            return $json;
    }

    public function ekseReff($fullname, $password, $nomor, $reff ,$token)
    {
        $curl = curl_init();
            curl_setopt_array($curl, [
            CURLOPT_URL => "https://algo-api.lionparcel.com/v3/account/auth/customer/register",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => "{\"city\":8863,\n \"fullname\":\"$fullname\",\n \"password\":\"$password\",\n \"password_confirm\":\"$password\",\n \"phone_number\":\"+62$nomor\",\n \"referral_code\":\"$reff\",\n\"token\":\"$token\"\n}",
            CURLOPT_HTTPHEADER => [
                "Content-Type: application/json"
            ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            curl_close($curl);
            $json = json_decode($response, true);
            return $json;
    }


}


$reff = trim(file_get_contents('reff.txt'));
$infoUser = new InformationHuman;
$lion = new LionParcel;
    echo "Input Nomor : ";
    $nomor = trim(fgets(STDIN));
    $ressCheckNomor = $lion->checkNomor($nomor);
            if($ressCheckNomor['login'] == false)
            {
                $ressRequestOtp = $lion->getOtp($nomor);
                    $otpId = trim($ressRequestOtp['otp_id']);
                    echo "\033[96mMeminta OTP. Harap Cek SMS..... \033[0m" . PHP_EOL;
                    echo "OTP : ";
                    $otp = trim(fgets(STDIN));
                    $ressValidateOtp = $lion->validateOtp($otpId, $otp);
                    $token = $ressValidateOtp['token'];


                    $fullname = $infoUser->getInfoUser()['firstName'] . " " . $infoUser->getInfoUser()['lastName'];
                    $password = $infoUser->getInfoUser()['password'];
                        $ressEkseReff = $lion->ekseReff($fullname, $password, $nomor, $reff ,$token);
                        if($ressEkseReff['success'] == true)
                        {
                            echo "\033[92mBerhasil Ngereff. Akun Akan disimpan ke file txt! \033[0m" . PHP_EOL;
                            echo "\033[92mInfo Akun : $fullname \033[0m" . PHP_EOL;
                            echo "\033[92mPassword Akun : $password \033[0m" . PHP_EOL;
                            echo "\033[92mReff : $reff \033[0m" . PHP_EOL;
                                $fileLion = fopen("resultAkun.txt", "a");
                                $resultData = "\nFull Name = $fullname\nNomor Telpon = $nomor\nPassword = $password\nPoint = 10.000\n=============================================";
                                $ressSave = fwrite($fileLion, $resultData);
                                fclose($fileLion);
                        }

            }else{
                echo "Nomor sudah terdaftar";
            }



    

