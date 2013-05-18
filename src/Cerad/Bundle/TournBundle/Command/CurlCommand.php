<?php
namespace Cerad\Bundle\TournBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

//e Symfony\Component\Console\Input\InputArgument;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CurlCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName       ('tourn:curl')
            ->setDescription('Curl Test')
          //->addArgument   ('inputFileName', InputArgument::REQUIRED, 'Input File Name')
          //->addArgument   ('truncate',      InputArgument::OPTIONAL, 'Truncate')
        ;
    }
    protected function getService($id)     { return $this->getContainer()->get($id); }
    protected function getParameter($name) { return $this->getContainer()->getParameter($name); }
    
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->testForm1();
    }
    protected function testForm1()
    {
        $host = 'http://local.zayso.org';
        $sub  = '/natgames2014';
        $url  = '/test/form1';
        $post = array('xxx' => 'xxx', 'zzz' => 'zzz');
        
        // create a new cURL resource
        $ch = curl_init();

        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $host . $sub . $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Prevents auto echoing
        curl_setopt($ch, CURLOPT_HEADER,         true); // Gives me my Set-Cookie
        curl_setopt($ch, CURLOPT_POST,           true);
      //curl_setopt($ch, CURLOPT_COOKIE,zayso_load_cookies());
    
        // Need the build to handle array of arrays
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post)); 

        // grab URL and pass it to the browser
        $response = curl_exec($ch);
        $info     = curl_getinfo($ch);
        
        //print_r($info);
        
        //echo $response;
        
        // close cURL resource, and free up system resources
        curl_close($ch);
        
        if (!isset($info['redirect_url'])) return;
        $redirect = substr($info['redirect_url'],strlen($host . $sub));
        echo sprintf("Redirected to: %s\n",$redirect);
    }
}
/* ==================
 * 
$ ./console tourn:curl
Array
(
    [url] => http://local.zayso.org/natgames2014/test/form1
    [content_type] => text/html; charset=UTF-8
    [http_code] => 302
    [header_size] => 349
    [request_size] => 162
    [filetime] => -1
    [ssl_verify_result] => 0
    [redirect_count] => 0
    [total_time] => 0.702
    [namelookup_time] => 0.015
    [connect_time] => 0.015
    [pretransfer_time] => 0.015
    [size_upload] => 15
    [size_download] => 385
    [speed_download] => 548
    [speed_upload] => 21
    [download_content_length] => -1
    [upload_content_length] => 15
    [starttransfer_time] => 0.702
    [redirect_time] => 0
    [certinfo] => Array
        (
        )

    [primary_ip] => 127.0.0.1
    [primary_port] => 80
    [local_ip] => 127.0.0.1
    [local_port] => 51078
    [redirect_url] => http://local.zayso.org/natgames2014/test/form1
)
HTTP/1.1 302 Found
Date: Sat, 18 May 2013 18:23:08 GMT
Server: Apache/2.4.3 (Win32) OpenSSL/1.0.1c PHP/5.4.7
X-Powered-By: PHP/5.4.7
Set-Cookie: PHPSESSID=65ifp5r6n1fc7br9iu5e6t2uq2; path=/
Cache-Control: no-cache
Location: /natgames2014/test/form1
X-Debug-Token: 2fda04
Transfer-Encoding: chunked
Content-Type: text/html; charset=UTF-8

<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <meta http-equiv="refresh" content="1;url=/natgames2014/test/form1" />

        <title>Redirecting to /natgames2014/test/form1</title>
    </head>
    <body>
        Redirecting to <a href="/natgames2014/test/form1">/natgames2014/test/form1</a>.
    </body>
</html>
ahundiak@GILES /c/home/ahundiak/zayso2016/aysonatgames/app (master)
$
 */
?>
