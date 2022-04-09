<?php

namespace App\Recaptcha;

// Service ParameterBagInterface qui nous permettra d'accéder aux paramètres globaux du site
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/*****  Service permettant de vérifier auprès de Google si un captcha est valide  *****/
// Pour fonctionner, le service a besoin de la clé privée présente dans le fichier .env
class RecaptchaValidator {

    // Stockage du service ParameterBagInterface de Symfony pour accéder aux paramètres globaux du site
    private $params;

    // Constructeur de la classe qui servira à demander le service ParameterBagInterface à Symfony et à hydrater $params avec ce dernier
    public function __construct(ParameterBagInterface $params)
    {
        $this->params = $params;
    }

    // Méthode servant à vérifier auprès de Google si le code captcha $recaptchaResponse et l'adresse IP $ip correspondent à un captcha correctement validé
    // La méthode retournera TRUE si le captcha est correcte, sinon FALSE
    public function verify(string $recaptchaResponse, string $ip = null){

        if (empty($recaptchaResponse)) {
            return FALSE;
        }

        $params = [
            'secret' => $this->params->get('google_recaptcha.private_key'),  // Récupération de la clé privée de Google Recaptcha depuis les paramètres du site
            'response' => $recaptchaResponse
        ];

        if ($ip) { $params['remoteip'] = $ip; }

        $url = "https://www.google.com/recaptcha/api/siteverify?" . http_build_query($params);

        if (function_exists('curl_version')) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_HEADER, false);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_TIMEOUT, 10);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            $response = curl_exec($curl);
        }
        else {
            $response = file_get_contents($url);
        }

        if (empty($response) || is_null($response)) { return FALSE; }

        return json_decode($response)->success;
    }

}