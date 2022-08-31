<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDO;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use Sendinblue\Mailin;
use Illuminate\Support\Str;

class EmailerController extends AppBaseController
{
    public function SendLink(Request $request){

        $input = $request->all();
        
        $code = rand(100000,999999);

        $find = $this->findUser($input['uemail']);
        
        if(!$find)
        {
             $invalid = array(
                'message'=>'User not found.',           
             );
            return response($invalid, 404)
            ->header('Content-Type','application/json');
        }
        
        $this->createResetPasswordToken($input['uemail']);

        $this->createEmailVerificationCode($code, $input['uemail']);

        $result = $this->retrieveResetToken($input['uemail']);

        $email = array('email'=>$input['uemail']);

        $result =  $this->sendEmailer($input['uemail'], $result->token, $code);

        return $email;
    }

    public function findUser($email) {
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from users where email = :email order by id desc');
        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt -> fetch();
        
        if(!$result)
        {
            return false;
        }
        
        return true;
    }
    public function createResetPasswordToken($email){

        $token = $this-> createResetToken(Str::random(40));

        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('insert into password_resets values (:email, :token, :created_at)');
        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->bindValue(':token', $token ,PDO::PARAM_STR);
        $stmt->bindValue(':created_at', now() ,PDO::PARAM_STR);
        $result = $stmt->execute();

        if(!$result)
        {
            $invalid = array(
                'message'=>'Token not created.',           
             );
            return response($invalid, 404)
            ->header('Content-Type','application/json');
        }        
    }
    public function createEmailVerificationCode($reset_code, $email){
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('update users set verification_code = :verification_code where email = :email order by id desc');
        $stmt->bindValue(':verification_code', $reset_code ,PDO::PARAM_STR);
        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->execute();       
    }
    public function retrieveResetToken($email){
        $con = $this -> defaultConnection();
        $stmt = $con -> prepare('select * from password_resets where email = :email order by created_at desc');
        $stmt->bindValue(':email', $email ,PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt -> fetch();
        
        if(!$result){
            return false;
        }
        return $result;

    }
    public function sendEmailer($email, $token, $code){
        /*
        config('base_url');
        */
        $link =  'http://localhost:3030/' . 'auth/new-password/' . $token . '?email=' . rawurlencode($email);
        $mail = new PHPMailer(true);
      
        try {            //Server settings
            $mail->SMTPDebug = 2;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp-relay.sendinblue.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'oxtempest98@gmail.com';                     //SMTP username
            $mail->Password   = 'rKt7H9fsSP5DdWwJ';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
            $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            //Recipients
            $mail->setFrom('oxtempest98@gmail.com', 'Batiboot');
            $mail->addAddress($email, 'Client');     //Add a recipient

            $mail->addReplyTo('oxtempest98@gmail.com', 'Information');
            $mail->addCC($email);
            $mail->addBCC($email);

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Reset Password Link';
            $mail->Body    =  $link .'<br>'.'CODE: '. $code;
        
            $mail->send();
           
            return $this->sendResponse([],'Email Sent Successfully',true);

        } catch (Exception $e) {
            return $this->sendResponse([],"Message could not be sent. Mailer Error: {$mail->ErrorInfo}",true); 
        }
    }
    public function sendResponse($data=array(),$message=null,$status=null)
	{
		$json_format = array(/* 
			'status' => $status,
			'message' => $message, */
			'data' => $data,
		);
		return json_encode($json_format);
	}
}
