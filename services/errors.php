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
        const INVALID_PARAM = 'INVALID_PARAM';
        const SERVER_DID_NOT_REPLY = 'SERVER_DID_NOT_REPLY';
        const SERVER_RESPONSE_NOT_VALID = 'SERVER_RESPONSE_NOT_VALID';
        const SERVER_SYSTEM_ERROR = 'SERVER_SYSTEM_ERROR';
    }

    $errorCodeTable = array (
        'INVALID_USER_ID' => 'There is no user registered with the provided information.',
        'NAME_IN_USE' => 'The user name is already in use. Please choose another user name.',
        'REGISTRATION_NOT_CONFIRMED' => 'Registration has not been confirmed.',
        'INVALID_LOGIN' => 'Your credentials do not match.',
        'SYSTEM_ERROR' => 'There was a system error processing your request. Information has been sent to support to remedy the problem.'
    );

    function errorToLocalString ($status_msg) {
        global $errorCodeTable;

        if (isset($errorCodeTable[$status_msg])) {
            $status_msg = $errorCodeTable[$status_msg];
        }
        return $status_msg;
    }