<?php

class Rest {

    protected $token;

    public function ConnectAPI($url)
    {
        $data = array("username" => "jlempis", "password" => "morpi005");
        $data_string = json_encode($data);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
        );

        $result = curl_exec($ch);
        $this->token = json_decode($result)->token;
        $returnCode = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);


        if ($returnCode != 200) {
            return false;
        }
        return $this->token;
    }

    public function CallApi($method, $url, $data = false)
    {
        $access_key = $this->token;
        $ch = curl_init($url);
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Bearer ' . $access_key ) );
        curl_exec($ch);
        $returnCode = curl_getinfo ($ch, CURLINFO_HTTP_CODE);

        if ($returnCode != 200) {
            return $returnCode;
        }

        return true;
    }
}
