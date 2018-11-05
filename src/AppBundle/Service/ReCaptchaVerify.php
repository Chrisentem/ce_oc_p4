<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 09/10/2018
 * Time: 11:03
 */

namespace AppBundle\Service;

class ReCaptchaVerify
{
    /**
     * @var string
     */
    private $reCaptchaSecretKey;

    public function __construct($reCaptchaSecretKey)
    {
        $this->reCaptchaSecretKey = $reCaptchaSecretKey;
    }

    /**
     * get success response from recaptcha and return it to controller
     *
     * @param $user_response
     * @return mixed
     */
    public function verify($user_response){
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        // Curl debug
//        curl_setopt($ch, CURLOPT_VERBOSE, true);
//        $verbose = fopen('php://temp', 'w+');
//        curl_setopt($ch, CURLOPT_STDERR, $verbose);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "secret"=> $this->reCaptchaSecretKey,
            "response"=>$user_response ]);
        $response = curl_exec($ch);
        // Curl debug
//        rewind($verbose);
//        $verboseLog = stream_get_contents($verbose);
//        echo "Verbose information:\n<pre>", htmlspecialchars($verboseLog), "</pre>\n";

        curl_close($ch);
        $data = json_decode($response);
        return $data;
    }
}