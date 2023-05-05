<?php
class PHP_Email_Form {
  public $to;
  public $from_name;
  public $from_email;
  public $subject;
  public $mailer;

  private $error_msg;

  public function __construct() {
    $this->mailer = new PHPMailer;
    $this->error_msg = '';
  }

  public function add_message( $content, $name = null ) {
    $this->mailer->Body .= '<p>'. $content .'</p>';
    $this->mailer->AltBody .= $content .'\n\n';
  }

  public function send() {
    $this->mailer->setFrom( $this->from_email, $this->from_name );
    $this->mailer->addAddress( $this->to );
    $this->mailer->Subject = $this->subject;

    if( ! $this->mailer->send() ) {
      $this->error_msg = 'Mailer Error: '. $this->mailer->ErrorInfo;
      return "Error: " . $this->error_msg;
    } else {
      return 'OK';
    }
  }

  public function validate_fields( $fields ) {
    foreach( $fields as $name => $value ) {
      if( isset( $_POST[$name] ) ) {
        $field_value = trim( $_POST[$name] );

        if( $value['required'] && empty( $field_value ) ) {
          $this->error_msg = 'Please enter '.$name.'!';
          return false;
        }

        if( ! empty( $field_value ) ) {
          switch( $value['type'] ) {
            case 'email':
              if( ! filter_var( $field_value, FILTER_VALIDATE_EMAIL ) ) {
                $this->error_msg = 'Please enter a valid '.$name.'!';
                return false;
              }
              break;

            case 'tel':
              if( ! preg_match( '/^\+?[0-9 ]+$/', $field_value ) ) {
                $this->error_msg = 'Please enter a valid '.$name.'!';
                return false;
              }
              break;
          }
        }
      }
    }

    return true;
  }
}
