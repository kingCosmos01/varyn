<?php
    /**
     * Document all error codes.
     * Error codes used in code are constants so they are easily used in algorithms and index lookup.
     * Not intended for UI consumption. Instead, use the string table and localized language to
     * map the error code into a UI string.
     *
     * Date: 2/7/16
     */

    abstract class EnginesisErrors {
        const NO_ERROR = '';
        const INVALID_PARAM = 'INVALID_PARAM';
        const INVALID_LOGIN = 'INVALID_LOGIN';
        const INVALID_USER_ID = 'INVALID_USER_ID';
        const SERVER_DID_NOT_REPLY = 'SERVER_DID_NOT_REPLY';
        const SERVER_RESPONSE_NOT_VALID = 'SERVER_RESPONSE_NOT_VALID';
        const SERVER_SYSTEM_ERROR = 'SERVER_SYSTEM_ERROR';
        const SYSTEM_ERROR = 'SYSTEM_ERROR';
        const INVALID_SECONDARY_PASSWORD = 'INVALID_SECONDARY_PASSWORD';
        const PASSWORD_EXPIRED = 'PASSWORD_EXPIRED';
        const NAME_IN_USE = 'NAME_IN_USE';
        const REGISTRATION_NOT_CONFIRMED = 'REGISTRATION_NOT_CONFIRMED';
        const INVALID_TOKEN = 'INVALID_TOKEN';
        const TOKEN_EXPIRED = 'TOKEN_EXPIRED';
    }

    $errorCodeTable = array (
        EnginesisErrors::SERVER_DID_NOT_REPLY => 'The service did not respond to our request. Please check your network connection.',
        EnginesisErrors::SERVER_RESPONSE_NOT_VALID => 'The service responded with an unexpected response. Please check your network connection and the operation of the service.',
        EnginesisErrors::SERVER_SYSTEM_ERROR => 'The service responded with an unexpected error. Please check your network connection and the operation of the service.',
        EnginesisErrors::INVALID_PARAM => 'A required parameter is missing or not valid in this context.',
        EnginesisErrors::INVALID_USER_ID => 'There is no user registered with the provided information.',
        EnginesisErrors::NAME_IN_USE => 'The user name is already in use. Please choose another user name.',
        EnginesisErrors::REGISTRATION_NOT_CONFIRMED => 'Registration has not been confirmed.',
        EnginesisErrors::INVALID_LOGIN => 'Your credentials do not match.',
        EnginesisErrors::INVALID_SECONDARY_PASSWORD => 'Your confirmation token is not valid or it has expired.',
        EnginesisErrors::PASSWORD_EXPIRED => 'Your confirmation token has expired.',
        EnginesisErrors::SYSTEM_ERROR => 'There was a system error processing your request. Information has been sent to support to remedy the problem.',
        EnginesisErrors::TOKEN_EXPIRED => 'The token you are trying to use is either invalid or it has past its expiration date.',
        EnginesisErrors::INVALID_TOKEN => 'The token you are trying to use is not valid.'
    );

    function errorToLocalString ($status_msg) {
        global $errorCodeTable;

        if (isset($errorCodeTable[$status_msg])) {
            $status_msg = $errorCodeTable[$status_msg];
        }
        return $status_msg;
    }