<?php
    /**
     * The MIT License
     * Copyright (c) 2007 Andy Smith
     */

    class TwitterOAuthRequest
    {
        protected $parameters;
        protected $httpMethod;
        protected $httpUrl;
        public static $version = '1.0';

        /**
         * Constructor
         *
         * @param string     $httpMethod
         * @param string     $httpUrl
         * @param array|null $parameters
         */
        public function __construct($httpMethod, $httpUrl, array $parameters = [])
        {
            $parameters = array_merge(self::parseParameters(parse_url($httpUrl, PHP_URL_QUERY)), $parameters);
            $this->parameters = $parameters;
            $this->httpMethod = $httpMethod;
            $this->httpUrl = $httpUrl;
        }

        /**
         * @param $input
         *
         * @return array|mixed|string
         */
        public static function urlencodeRfc3986($input)
        {
            $output = '';
            if (is_array($input)) {
                for ($i = 0; $i < count($input); $i ++) {
                    $input[$i] = self::urlencodeRfc3986($input[$i]);
                }
                $output = $input;
            } elseif (is_scalar($input)) {
                $output = rawurlencode($input);
            }
            return $output;
        }

        /**
         * @param $input
         * @return array
         */
        public static function parseParameters($input)
        {
            if (!is_string($input)) {
                return [];
            }

            $pairs = explode('&', $input);

            $parameters = [];
            foreach ($pairs as $pair) {
                $split = explode('=', $pair, 2);
                $parameter = urldecode($split[0]);
                $value = isset($split[1]) ? urldecode($split[1]) : '';

                if (isset($parameters[$parameter])) {
                    // We have already recieved parameter(s) with this name, so add to the list
                    // of parameters with this name

                    if (is_scalar($parameters[$parameter])) {
                        // This is the first duplicate, so transform scalar (string) into an array
                        // so we can add the duplicates
                        $parameters[$parameter] = [$parameters[$parameter]];
                    }

                    $parameters[$parameter][] = $value;
                } else {
                    $parameters[$parameter] = $value;
                }
            }
            return $parameters;
        }

        /**
         * @param array $params
         *
         * @return string
         */
        public static function buildHttpQuery(array $params)
        {
            if (empty($params)) {
                return '';
            }

            // Urlencode both keys and values
            $keys = self::urlencodeRfc3986(array_keys($params));
            $values = self::urlencodeRfc3986(array_values($params));
            $params = array_combine($keys, $values);

            // Parameters are sorted by name, using lexicographical byte value ordering.
            // Ref: Spec: 9.1.1 (1)
            uksort($params, 'strcmp');

            $pairs = [];
            foreach ($params as $parameter => $value) {
                if (is_array($value)) {
                    // If two or more parameters share the same name, they are sorted by their value
                    // Ref: Spec: 9.1.1 (1)
                    // June 12th, 2010 - changed to sort because of issue 164 by hidetaka
                    sort($value, SORT_STRING);
                    foreach ($value as $duplicateValue) {
                        $pairs[] = $parameter . '=' . $duplicateValue;
                    }
                } else {
                    $pairs[] = $parameter . '=' . $value;
                }
            }
            // For each parameter, the name is separated from the corresponding value by an '=' character (ASCII code 61)
            // Each name-value pair is separated by an '&' character (ASCII code 38)
            return implode('&', $pairs);
        }

        /**
         * pretty much a helper function to set up the request
         *
         * @param TwitterOAuthConsumer $consumer
         * @param TwitterOAuthToken    $token
         * @param string   $httpMethod
         * @param string   $httpUrl
         * @param array    $parameters
         *
         * @return Request
         */
        public static function fromConsumerAndToken(
            TwitterOAuthConsumer $consumer,
            TwitterOAuthToken $token = null,
            $httpMethod,
            $httpUrl,
            array $parameters = []
        ) {
            $defaults = [
                "oauth_version" => self::$version,
                "oauth_nonce" => self::generateNonce(),
                "oauth_timestamp" => time(),
                "oauth_consumer_key" => $consumer->key
            ];
            if (null !== $token) {
                $defaults['oauth_token'] = $token->key;
            }

            $parameters = array_merge($defaults, $parameters);

            return new TwitterOAuthRequest($httpMethod, $httpUrl, $parameters);
        }

        /**
         * @param string $name
         * @param string $value
         */
        public function setParameter($name, $value)
        {
            $this->parameters[$name] = $value;
        }

        /**
         * @param $name
         *
         * @return string|null
         */
        public function getParameter($name)
        {
            return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
        }

        /**
         * @return array
         */
        public function getParameters()
        {
            return $this->parameters;
        }

        /**
         * @param $name
         */
        public function removeParameter($name)
        {
            unset($this->parameters[$name]);
        }

        /**
         * The request parameters, sorted and concatenated into a normalized string.
         *
         * @return string
         */
        public function getSignableParameters()
        {
            // Grab all parameters
            $params = $this->parameters;

            // Remove oauth_signature if present
            // Ref: Spec: 9.1.1 ("The oauth_signature parameter MUST be excluded.")
            if (isset($params['oauth_signature'])) {
                unset($params['oauth_signature']);
            }

            return TwitterOAuthRequest::buildHttpQuery($params);
        }

        /**
         * Returns the base string of this request
         *
         * The base string defined as the method, the url
         * and the parameters (normalized), each urlencoded
         * and the concated with &.
         *
         * @return string
         */
        public function getSignatureBaseString()
        {
            $parts = [
                $this->getNormalizedHttpMethod(),
                $this->getNormalizedHttpUrl(),
                $this->getSignableParameters()
            ];
            $parts = self::urlencodeRfc3986($parts);
            return implode('&', $parts);
        }

        /**
         * Returns the HTTP Method in uppercase
         *
         * @return string
         */
        public function getNormalizedHttpMethod()
        {
            return strtoupper($this->httpMethod);
        }

        /**
         * parses the url and rebuilds it to be
         * scheme://host/path
         *
         * @return string
         */
        public function getNormalizedHttpUrl()
        {
            $parts = parse_url($this->httpUrl);

            $scheme = $parts['scheme'];
            $host = strtolower($parts['host']);
            $path = $parts['path'];

            return "$scheme://$host$path";
        }

        /**
         * Builds a url usable for a GET request
         *
         * @return string
         */
        public function toUrl()
        {
            $postData = $this->toPostdata();
            $out = $this->getNormalizedHttpUrl();
            if ($postData) {
                $out .= '?' . $postData;
            }
            return $out;
        }

        /**
         * Builds the data one would send in a POST request
         *
         * @return string
         */
        public function toPostdata()
        {
            return self::buildHttpQuery($this->parameters);
        }

        /**
         * Builds the Authorization: header
         *
         * @return string
         * @throws TwitterOAuthException
         */
        public function toHeader()
        {
            $first = true;
            $out = 'Authorization: OAuth';
            foreach ($this->parameters as $k => $v) {
                if (substr($k, 0, 5) != "oauth") {
                    continue;
                }
                if (is_array($v)) {
                    throw new TwitterOAuthException('Arrays not supported in headers');
                }
                $out .= ($first) ? ' ' : ', ';
                $out .= rawurlencode($k) . '="' . rawurlencode($v) . '"';
                $first = false;
            }
            return $out;
        }

        /**
         * @return string
         */
        public function __toString()
        {
            return $this->toUrl();
        }

        /**
         * @param SignatureMethod $signatureMethod
         * @param TwitterOAuthConsumer        $consumer
         * @param TwitterOAuthToken           $token
         */
        public function signRequest(SignatureMethod $signatureMethod, TwitterOAuthConsumer $consumer, TwitterOAuthToken $token = null)
        {
            $this->setParameter("oauth_signature_method", $signatureMethod->getName());
            $signature = $this->buildSignature($signatureMethod, $consumer, $token);
            $this->setParameter("oauth_signature", $signature);
        }

        /**
         * @param SignatureMethod $signatureMethod
         * @param TwitterOAuthConsumer        $consumer
         * @param TwitterOAuthToken           $token
         *
         * @return string
         */
        public function buildSignature(SignatureMethod $signatureMethod, TwitterOAuthConsumer $consumer, TwitterOAuthToken $token = null)
        {
            return $signatureMethod->buildSignature($this, $consumer, $token);
        }

        /**
         * @return string
         */
        public static function generateNonce()
        {
            return md5(microtime() . mt_rand());
        }
    }