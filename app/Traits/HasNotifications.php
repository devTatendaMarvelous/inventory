<?php

namespace App\Traits;

use App\Traits\HasCurlRequests;

trait HasNotifications
{
    use ApiConsume;

    public function emailNotification($emails,$subject,$body,$attachment=null)
    {
        $emails=collect($emails);
        $emails->each(function ($email) use ($body,$subject) {
            $data =  [
                "emails" => [
                    [
                        "receipient" => [
                            "email" => $email
                        ],
                        "subject" => $subject,
                        "bodyText" => $body
                    ]
                ]
            ];

            $this->postRequest("/emails/send", $data,false,'notifications');
        });
    }

    public function generateClaimChargeBody( $data)
    {
        return"
    <p>Dear Claims Team</p>
    <p>Client $data->client has submitted a New Claim with the details below :</p>
    <ul>
        <li>Claim Reference: $data->id</li>
        <li>Case type: Civil Case</li>
        <li>Nature of Case: $data->nature_of_case </li>
        <li>Charge Name: $data->charge_name </li>
        <li>Amount: $data->amount</li>
        <li>Currency: $data->currency </li>
        <li>Status: $data->status </li>
    </ul>
    <p>Please attend to claim promptly by clicking on the link below:</p>

    <a href='https://coverlink.logarithm.co.zw/claim-view/$data->id'>Review New Clam<a>";

//
//        "<p>Good day,</p>
//<p>A claim charge has been processed with the following details, please process the claim.</p>
//<ul>
//    <li>Claim Reference: $data->id </li>
//    <li>Charge Name: $data->charge_name </li>
//    <li>Amount: $data->amount </li>
//    <li>Currency: $data->currency </li>
//    <li>Status: $data->status </li>
//</ul>
//<p>Thank you for your attention.</p>";

    }

    public function sendOTP($phone,$otp)
    {
        try {
            $data =[
                "mobiles"=> $phone,
                "sms"=>"Dear Customer, your verification code is $otp, valid for 5 minutes."
            ];
            $this->postRequest("/send", $data,false,'sms');
            return true;
        }catch (\Exception $exception){
            return false;
        }

    }

}
