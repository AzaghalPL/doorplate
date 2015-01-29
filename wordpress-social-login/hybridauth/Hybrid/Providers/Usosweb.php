<?php
/*!
* HybridAuth
* http://hybridauth.sourceforge.net | http://github.com/hybridauth/hybridauth
* (c) 2009-2012, HybridAuth authors | http://hybridauth.sourceforge.net/licenses.html 
*/

/**
* Hybrid_Providers_Usosweb provider adapter based on OAuth1 protocol
* Adapter to Usosweb API by Henryk Michalewski
*/

class Hybrid_Providers_Usosweb extends Hybrid_Provider_Model_OAuth1
{
	/**
	* IDp wrappers initializer 
	*/
	/* Required scopes. The only functionality of this application is to say hello,
    * so it does not really require any. But, if you want, you may access user's
    * email, just do the following:
    * - put array('email') here,
    * - append 'email' to the 'fields' argument of 'services/users/user' method,
    *   you will find it below in this script.
    */
	
	function initialize()
	{
		parent::initialize();
		
        $scopes = array("photo", "grades");

		// Provider api end-points 
		$this->api->api_base_url      = "https://usosapps.uw.edu.pl/";
		$this->api->request_token_url = "https://usosapps.uw.edu.pl/services/oauth/request_token?scopes=".implode("|", $scopes);
		$this->api->access_token_url  = "https://usosapps.uw.edu.pl/services/oauth/access_token";
		$this->api->authorize_url = "https://usosapps.uw.edu.pl/services/oauth/authorize";

	}
	
	
    /**
	* begin login step 
	*/
	function loginBegin()
	{
		$tokens = $this->api->requestToken( $this->endpoint ); 

		// request tokens as received from provider
		$this->request_tokens_raw = $tokens;
		
		// check the last HTTP status code returned
		if ( $this->api->http_code != 200 ){
			throw new Exception( "Authentication failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ), 5 );
		}

		if ( ! isset( $tokens["oauth_token"] ) ){
			throw new Exception( "Authentication failed! {$this->providerId} returned an invalid oauth_token.", 5 );
		}

		$this->token( "request_token"       , $tokens["oauth_token"] ); 
		$this->token( "request_token_secret", $tokens["oauth_token_secret"] ); 

		# redirect the user to the provider authentication url
		Hybrid_Auth::redirect( $this->api->authorizeUrl( $tokens ) );
	}
		

	/**
	* load the user profile from the IDp api client
	*/
	function getUserProfile()
	{
		$response = $this->api->get( 'https://usosapps.uw.edu.pl/services/users/user?fields=id|first_name|last_name|sex|homepage_url|profile_url|photo_urls[200x200]' );

		// check the last HTTP status code returned
		if ( $this->api->http_code != 200 ){
			throw new Exception( "User profile request failed! {$this->providerId} returned an error. " . $this->errorMessageByStatus( $this->api->http_code ), 6 );
		}

		if ( ! is_object( $response ) || ! isset( $response->id ) ){
			throw new Exception( "User profile request failed! {$this->providerId} api returned an invalid response.", 6 );
		}

		# store the user profile. 
		# written without a deeper study what is really going on in Usosweb API
		 
		$this->user->profile->identifier  = (property_exists($response,'id'))?$response->id:"";
		$this->user->profile->displayName = (property_exists($response,'first_name') && property_exists($response,'last_name'))?$response->first_name." ".$response->last_name:"";
		$this->user->profile->lastName   = (property_exists($response,'last_name'))?$response->last_name:""; 
		$this->user->profile->firstName   = (property_exists($response,'first_name'))?$response->first_name:""; 
        $this->user->profile->gender = (property_exists($response,'sex'))?$response->sex:"";
		$this->user->profile->photoURL  = (property_exists($response,'photo_urls'))?$response->photo_urls->{'200x200'}:"";
		$this->user->profile->profileURL  = (property_exists($response,'profile_url'))?$response->profile_url:"";
		$this->user->profile->webSiteURL  = (property_exists($response,'homepage_url'))?$response->homepage_url:""; 

		return $this->user->profile;
 	}

}
