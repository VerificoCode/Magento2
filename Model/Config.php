<?php
namespace Verifico\Ageverify\Model;

class Config
{

    public function getUnityName() {
        return "AgeChecked Plus+";
    }

    public function getUnityUrl() {
        return "https://agechecked.verifico.io";
        // return "https://unity.agechecked.com";
    }

    public function getUnityOrderUrl() {
        return self::getUnityUrl()."/r/new-order-webhook/";
    }

    public function getUnityIntegrationRef() {
        return "AgeCheckedPlus+Verifico";
    }
}
