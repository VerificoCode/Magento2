<?php
namespace Verifico\Ageverify\Model;

class Config
{

    public function getUnityName() {
        return "AgeChecked Plus+";
    }

    public function getUnityUrl() {
        // return "https://verifico.co.uk";
        return "https://staging.unity.agechecked.com";
        // return "https://unity.agechecked.com";
    }

    public function getUnityIntegrationName() {
        return "AgeChecked Plus+ ";
    }

    public function getUnityIntegrationEmail() {
        return "unity@agechecked.com";
    }

    public function getUnityIntegrationEndpoint() {
        return self::getUnityUrl()."/setup/magento-callback/";
        // This needs to be a unity endpoint
    }

    public function getUnityRegisterUrl() {
        return self::getUnityUrl()."/setup/auto-register/";
    }

    public function getUnityJsUrl() {

    }

    public function getUnityIntegrationRef() {
        return "AgeCheckedPlus+Verifico";
    }

    

    

    

}


