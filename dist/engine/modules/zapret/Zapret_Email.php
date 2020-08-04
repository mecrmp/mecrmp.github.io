<?php

    class ZapretEmail
    {
        //file in utf-8 charset !!!
        public function __construct()
        {
        }

        public function send ($title,$text,$charset,$sender_email,$my_partner_email, $senderIp)
        {
            if (!trim ($my_partner_email))
                $my_partner_email = "noemail@noemail.com";

            $url = "http://zapret-rf.org/Service2/saveCopyrightLetter";
            $data=array();
            $data["url"]=$url;
            $data["title"]=$title;
            $data["sender_email"]=$sender_email;
            $data["my_partner_email"]=$my_partner_email;
            $data["text"]=$text;

            if(strtolower($charset)!='utf-8')
            {
                $data["title"]=iconv($charset,'utf-8',$data["title"]);
                $data["text"]=iconv($charset,'utf-8',$data["text"]);
            }

            if(!$this->letterFromCopyright($data))
                return false;

            if (!$senderIp)
                $bun_ip=$this->getIp();
            else
                $bun_ip = $senderIp;

            $domainObj = parse_url($this->currentPageUrl());
            $currentDomain = $domainObj["host"];
            $data["domain"]=$currentDomain;
            $data["bun_ip"]=$bun_ip;

            $response=$this->send_request($data);
            return  $response;
        }

        public function letterFromCopyright($data)
        {
            $text=$data["title"]." ".$data["text"];
            $key=array("copyright","intellectual", "правообла", "интеллектуа");
            foreach($key as $k)
            {
                if(mb_strpos($text,$k)!==false)
                    return true;
            }

            return false;
        }

        public function getIp ()
        {
            $_SERVER['REMOTE_ADDR'] = isset($_SERVER["HTTP_CF_CONNECTING_IP"]) ? $_SERVER["HTTP_CF_CONNECTING_IP"] : $_SERVER["REMOTE_ADDR"];

            if ($_SERVER['REMOTE_ADDR'] != "127.0.0.1")
                return $_SERVER['REMOTE_ADDR'];

            if (isset ($_SERVER['HTTP_X_FORWARD_FOR']))
                $ip = $_SERVER['HTTP_X_FORWARD_FOR'];
            else
                $ip = $_SERVER['REMOTE_ADDR'];

            if ($ip == "127.0.0.1")
            {
                if (isset ($_SERVER["HTTP_X_REAL_IP"]))
                    $ip = $_SERVER["HTTP_X_REAL_IP"];
            }

            return $ip;
        }
        public function currentPageUrl()
        {
            $pageURL = 'http';
            if (@$_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
            $pageURL .= "://";


            if ($_SERVER["SERVER_PORT"] != "80") {
                $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
            } else {

                $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
            }

            return $pageURL;
        }

        public function send_request ($data)
        {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL,$data["url"]);
            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_POSTFIELDS,
                http_build_query($data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec ($ch);
            curl_close ($ch);
            if ($server_output == "OK")
                return true;
            else
                return false;
        }
    }
?>