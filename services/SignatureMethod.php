<?php
    /**
     * The MIT License
     * Copyright (c) 2007 Andy Smith
     */

    /**
     * A class for implementing a Signature Method
     * See section 9 ("Signing Requests") in the spec
     */
    abstract class SignatureMethod
    {
        /**
         * Needs to return the name of the Signature Method (ie HMAC-SHA1)
         *
         * @return string
         */
        abstract public function getName();

        /**
         * Build up the signature
         * NOTE: The output of this function MUST NOT be urlencoded.
         * the encoding is handled in OAuthRequest when the final
         * request is serialized
         *
         * @param TwitterOAuthRequest $request
         * @param TwitterOAuthConsumer $consumer
         * @param TwitterOAuthToken $token
         *
         * @return string
         */
        abstract public function buildSignature(TwitterOAuthRequest $request, TwitterOAuthConsumer $consumer, TwitterOAuthToken $token = null);

        /**
         * Verifies that a given signature is correct
         *
         * @param TwitterOAuthRequest $request
         * @param TwitterOAuthConsumer $consumer
         * @param TwitterOAuthToken $token
         * @param string $signature
         *
         * @return bool
         */
        public function checkSignature(TwitterOAuthRequest $request, TwitterOAuthConsumer $consumer, TwitterOAuthToken $token, $signature)
        {
            $built = $this->buildSignature($request, $consumer, $token);

            // Check for zero length, although unlikely here
            if (strlen($built) == 0 || strlen($signature) == 0) {
                return false;
            }

            if (strlen($built) != strlen($signature)) {
                return false;
            }

            // Avoid a timing leak with a (hopefully) time insensitive compare
            $result = 0;
            for ($i = 0; $i < strlen($signature); $i++) {
                $result |= ord($built{$i}) ^ ord($signature{$i});
            }

            return $result == 0;
        }
    }