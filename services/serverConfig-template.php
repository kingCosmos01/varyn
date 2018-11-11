<?php
    /**
     * Define sensitive data in this configuration file. If serverConfig.php is missing, then it should
     * be setup like this.
     * User: jf
     * Date: Feb-13-2016
     */

    // From EnginesisNetworks::enum, but this should not depend on that include so the enums are hardcoded.
    $socialServiceKeys = [
        2  => ['service' => 'Facebook', 'app_id' => '', 'app_secret' => '', 'admins' =>''],
        7  => ['service' => 'Google', 'app_id' => '', 'app_secret' => '', 'admins' =>''],
        11 => ['service' => 'Twitter', 'app_id' => '', 'app_secret' => '', 'admins' =>'']
    ];
    $developerKey = '';
