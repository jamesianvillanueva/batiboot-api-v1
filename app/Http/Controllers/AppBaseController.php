<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use PDO;
use ReallySimpleJWT\Token;

class AppBaseController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    // VALIDATE HEADERS - API ACCESS KEY AND ACCESS TOKEN

    /* public function CheckAPIHeader($request) {
        
        if(!$request -> hasHeader('x-api-key')){
            $invalid = array(
                'message' => 'Missing Request Header'
            );
            return response($invalid,400)
            ->header('Content-Type','application/json');
        }
        $signature = $request->header('x-api-key');
        // VALIDATE API KEY
        $secret = env('SECRET_API_KEY');

		$result = ($signature == $secret);

        if(!$result)
        {
            $invalid = array(
                'message'=>'Invalid Access Key.',           
             );
            return response($invalid, 401)
            ->header('Content-Type','application/json');
        }

        return true;
    } */

    static function db ( $conn )
	{
		if ( is_array( $conn ) ) {
			return self::sql_connect( $conn );
		}

		return $conn;
	}

    static function sql_connect ( $sql_details )
	{
		try {
			$type = $sql_details['type'] === 1 ?  PDO::FETCH_OBJ : PDO::FETCH_ASSOC;
			$db = @new PDO(
				"mysql:host={$sql_details['host']};dbname={$sql_details['db']}",
				$sql_details['user'],
				$sql_details['pass'],
				array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, 
					   PDO::ATTR_DEFAULT_FETCH_MODE => $type,
                       PDO::ATTR_EMULATE_PREPARES   => false)
			);
		}
		catch (PDOException $e) {
			self::fatal(
				"An error occurred while connecting to the database. ".
				"The error reported by the server was: ".$e->getMessage()
			);
		}

		return $db;
	}

    // DECLARE Connection
    public function defaultConnection($type=1) {
        $sqlconn = array(
            'host' => env('DB_HOST'),
            'db' => env('DB_DATABASE'),
            'user' => env('DB_USERNAME'),
            'pass' => env('DB_PASSWORD'),
            'type' => $type,
        );
        $con = self::db($sqlconn);
        return $con;
    }

    public function createWebToken($id , $uname)
	{
		$payload = [
			'iat' => time(),
			'userId' => $id,
			'u'=> $uname,
			'exp' => time() + 3600, //seconds webtoken expiration 	
			'iss' => 'v1-batiboot-api'
		];
		$secret = 'Secret&LoveSong143==xD';	
		$token = Token::customPayload($payload, $secret);
		return $token;
	}

	public function createResetToken($token){
		$payload = [
			'iat' => time(),
		//	'userId' => $id,
			'u'=> $token,
			'exp' => time() + 180, //seconds webtoken expiration 	
			'iss' => 'v1-batiboot-api'
		];
		$secret = 'Secret&LoveSong143==xD';	

		$passwordToken = Token::customPayload($payload, $secret);

		return $passwordToken;
	}
    public function validateToken($token)
	{
		$secret = 'Secret&LoveSong143==xD';
		$result = Token::validate($token, $secret);
		return $result;
	}
	public function getPayload($token)
	{
		$secret = 'Secret&LoveSong143==xD';
		$payload = Token::getPayload($token, $secret);
		return $payload;
	}
}
