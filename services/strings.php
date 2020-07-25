<?php
    /**
     * String table lookup helper functions and string key definitions.
     * In order to support localization in the PHP code we use a string lookup table that
     * defaults to English if the look up for the language code fails.
     *
     * @date: 7/29/2017
     */

    require_once('locale/en/strings_en.php'); // TODO: should actually be the $this->_defaultLanguageCode

    /**
     * Class EnginesisUIStrings
     * Defines all of the available UI strings in the string tables.
     */
    abstract class EnginesisUIStrings {
        const MISSING_STRING               = 'MISSING_STRING';
        const CANNOT_LOG_IN                = 'CANNOT_LOG_IN';
        const NAME_PASSWORD_MISMATCH       = 'NAME_PASSWORD_MISMATCH';
        const LOGIN_SYSTEM_FAILURE         = 'LOGIN_SYSTEM_FAILURE';
        const REG_INFO_INCOMPLETE          = 'REG_INFO_INCOMPLETE';
        const REGISTRATION_NAME_IN_USE     = 'NAME_IN_USE';
        const REGISTRATION_EMAIL_IN_USE    = 'EMAIL_IN_USE';
        const REGISTRATION_INVALID         = 'INVALID_USER_NAME';
        const REGISTRATION_ERROR           = 'REGISTRATION_ERROR';
        const REGISTRATION_NOT_ACCEPTED    = 'REGISTRATION_NOT_ACCEPTED';
        const REGISTRATION_ACCEPTED        = 'REGISTRATION_ACCEPTED';
        const REG_INFO_UPDATED             = 'REG_INFO_UPDATED';
        const MUST_BE_LOGGED_IN            = 'MUST_BE_LOGGED_IN';
        const PROFILE_PAGE_SALUTATION      = 'PROFILE_PAGE_SALUTATION';
        const SYSTEM_ERROR                 = 'SYSTEM_ERROR';
        const REFRESH_TOKEN_ERROR          = 'REFRESH_TOKEN_ERROR';
        const REGISTRATION_ERRORS_FIELDS   = 'REGISTRATION_ERRORS_FIELDS';
        const SECURITY_ERRORS_FIELDS       = 'SECURITY_ERRORS_FIELDS';
        const WELCOME_MESSAGE              = 'WELCOME_MESSAGE';
        const REG_RESET_PASSWORD           = 'REG_RESET_PASSWORD';
        const REG_COMPLETE_RESET_MESSAGE   = 'REG_COMPLETE_RESET_MESSAGE';
        const REDIRECT_CONFIRM_MESSAGE     = 'REDIRECT_CONFIRM_MESSAGE';
        const LOGOUT_COMPLETE              = 'LOGOUT_COMPLETE';
        const SSO_EXCEPTION                = 'SSO_EXCEPTION';
    }

    class EnginesisStringTable
    {
        private $_siteId = 1000;
        private $_languageCode = 'en';
        private $_defaultLanguageCode = 'en';
        private $_uiStringTable = null;
        private $_defaultStringTable = null;
        private $_localePath = '';
        private $_supportedLocales = array('en', 'es');

        /**
         * EnginesisStringTable constructor. Construct the object and load the default and in-effect language tables.
         * @param $siteId
         * @param $languageCode
         */
        public function __construct($siteId, $languageCode) {
            global $enginesis_strings_en;
            global $serverRootPath; // from common.php, points to website root above /public/ and ends with /

            $this->_siteId = $siteId;
            $this->_languageCode = strtolower($languageCode);
            $this->_localePath = $serverRootPath . 'services' . DIRECTORY_SEPARATOR . 'locale' . DIRECTORY_SEPARATOR;

            if (isset($enginesis_strings_en)) { // loaded from './locale/en/strings_en.php'
                $this->_defaultStringTable = $enginesis_strings_en;
            }
            if ($this->_languageCode != $this->_defaultLanguageCode && $this->isValidLocale($this->_languageCode)) {
                $this->loadUIStringTable();
            }
        }

        /**
         * Determine if a language code is supported.
         * @param $languageCode
         * @return bool Returns true if the language code is supported.
         */
        public function isValidLocale($languageCode) {
            return in_array($languageCode, $this->_supportedLocales);
        }

        /**
         * Set the language after construction. If the language does'nt change, nothing is done. If the language
         * changes the new language table is loaded.
         * @param $languageCode
         * @return bool
         */
        public function setLocale($languageCode) {
            $isLoaded = true;
            $languageCode = strtolower($languageCode);
            if ($languageCode != $this->_languageCode && $this->isValidLocale($languageCode)) {
                $this->_languageCode = $languageCode;
                $isLoaded = $this->loadUIStringTable();
            }
            return $isLoaded;
        }

        /**
         * @param $stringKey string from EnginesisUIStrings
         * @param $parameters array of key => values to replace %tokens% in string.
         * @return string
         */
        public function lookup($stringKey, $parameters = null) {
            $string = null;
            if ($this->_uiStringTable == null && $this->_languageCode != $this->_defaultLanguageCode) {
                $this->loadUIStringTable();
            }
            if ($this->_uiStringTable !== null && isset($this->_uiStringTable[$stringKey])) {
                $string = $this->_uiStringTable[$stringKey];
            }
            if ($string == null) {
                $string = $this->lookupDefault($stringKey, $parameters);
            }
            if ($string != null && $parameters != null && strpos($string, '%') >= 0) {
                $string = tokenReplace($string, $parameters);
            }
            return $string;
        }

        /**
         * Look up a string key in the default language table. Typically this function is called when the look up
         * in the in-effect language table fails and we want to try a default fall-back.
         * @param $stringKey
         * @return null|string
         */
        private function lookupDefault($stringKey) {
            $string = null;

            if ($this->_defaultStringTable == null) {
                $string = 'SYSTEM ERROR: localization system not properly initialized.';
            } else {
                if (isset($this->_defaultStringTable[$stringKey])) {
                    $string = $this->_defaultStringTable[$stringKey];
                }
                if ($string == null) {
                    $string = $this->_defaultStringTable[EnginesisUIStrings::MISSING_STRING];
                }
            }
            return $string;
        }

        /**
         * Load a language string table based on the set language code. Language string tables are a specific
         * format and in a specific location. This will overwrite any prior language table as there can be only
         * one (and the default English table is always available as a fall back.)
         * @return bool Returns true if a language table is loaded or already loaded. Returns false if an error occurred.
         */
        private function loadUIStringTable() {
            $fileToLoad = $this->_localePath . $this->_languageCode . '/strings_' . $this->_languageCode . '.php';
            if (file_exists($fileToLoad)) {
                require_once($fileToLoad);
                if (isset(${'enginesis_strings_' . $this->_languageCode})) {
                    $this->_uiStringTable = ${'enginesis_strings_' . $this->_languageCode};
                    $error = '';
                } else {
                    $error = 'SYSTEM ERROR: localization table for ' . $this->_languageCode . ' exists but not properly defined.';
                }
            } else {
                $error = 'SYSTEM ERROR: localization table for ' . $this->_languageCode . ' not defined at ' . $fileToLoad . '.';
            }
            if ($error != '') {
                debugLog('EnginesisStringTable::loadUIStringTable ' . $error);
            }
            return $error == '';
        }
    }