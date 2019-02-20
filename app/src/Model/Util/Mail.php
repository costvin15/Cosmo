<?php
/**
 * Created by PhpStorm.
 * User: dilsonrabelo
 * Date: 15/09/17
 * Time: 08:56
 */

namespace App\Model\Util;


use App\Facilitator\App\ContainerFacilitator;
use PHPMailer\PHPMailer\PHPMailer;

class Mail
{
    public static function send(array $from, array $address, $subject, $body) {

        return true;

        $ci = ContainerFacilitator::getContainer();
        $apiKey = $ci->get('settings')->get('apikey.sendgrid');

        //Send mail to user with the new password
        $phpMailer = new PHPMailer(true);

        //Server settings
        $phpMailer->isSMTP();
        $phpMailer->Host = 'smtp.sendgrid.net';
        $phpMailer->SMTPAuth = true;
        $phpMailer->Username = 'apikey';
        $phpMailer->Password = $apiKey;
        $phpMailer->SMTPSecure = 'tls';
        $phpMailer->Port = 587;

        //Recipients
        $phpMailer->setFrom($from['mail'], $from['fullname']);

        foreach($address as $mail) {
            $phpMailer->addAddress($mail['mail'], $mail['fullname']);
        }

        //Content
        $phpMailer->isHTML(true);
        $phpMailer->Subject = $subject;
        $phpMailer->Body    = utf8_decode($body);

        $phpMailer->send();
    }
}