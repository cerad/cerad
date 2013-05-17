<?php

/* ==============================================
 * For testing
 */
if (!function_exists('get_option'))
{
    function get_option($name)
    {
        switch($name)
        {
            case 'zayso_host':         return 'http://local.zayso.org';
            case 'zayso_session_path': return '.';
        }
    }
}
function zayso_session_start($id = null)
{
    if (!session_id())
    {
        if ($id) session_id($id);
        if (get_option('zayso_session_path')) session_save_path(get_option('zayso_session_path)'));
        session_start();
    }
    $_SESSION['zayso_time'] = time();
}
function zayso_save_cookies($header)
{
    if (isset($_SESSION['zayso_cookies'])) $cookies = $_SESSION['zayso_cookies'];
    else                                   $cookies = array();
    
    $matches = null;
    preg_match_all('/^Set-Cookie:\s*([^;]*)/mi',$header, $matches);
    
    if (!isset($matches[1])) return;
    foreach($matches[1] as $match)
    {
        parse_str($match,$cookie);
        $cookies = array_merge($cookies,$cookie);
    }
    $_SESSION['zayso_cookies'] = $cookies;
}
function zayso_load_cookies()
{
    if (isset($_SESSION['zayso_cookies'])) $cookies = $_SESSION['zayso_cookies'];
    else                                   $cookies = array();
    $buf = null;
    foreach($cookies as $key => $value)
    {
        if ($buf) $buf .= '; ';
        $buf .= $key . '=' . $value;
    }
    return $buf;
}
function zayso_extract_fragment($body,$id = null)
{
    if ($id == 'none') return null;
    
    $dom = new DOMDocument(); 

    // Load the html's contents into DOM 
    $dom->loadHTML($body);
    $dom->formatOutput = true;
    
    if (!$id) return $dom->saveHTML();
    
    $fragment = $dom->getElementById($id);
    if ($fragment) 
    {
        $dom2 = new DOMDocument();
        $node = $dom2->importNode($fragment,true);
        $dom2->appendChild($node);
        
        $dom2->formatOutput = true;
        return $dom2->saveHTML();
        
        // PHP 5.5.6
        return $dom->saveHTML($fragment);
    }
    return $dom->saveHTML();
    
    // get each form in a DOMNodeList
    $forms = $dom->getElementsByTagName('form');

    // Yank out the html for the first form found
    if ($forms->length)
    {
        $form = $forms->item(0);
        
        $dom->formatOutput = true;
        echo $dom->saveHTML($form) . "\n";
        
    }
    foreach ($forms as $form) 
    {
        // if you know the form name attribute, you could check it here before continuing...
        // with $form->getAttribute('name'), and the continue with next iteration if not the right one
        // loop throught each input tags in the form
        $inputs = $form->getElementsByTagName('input');
        foreach ($inputs as $input) 
        {
            // get input name attribute and value and ...
            $inputName  = $input->getAttribute('name');
            $inputValue = $input->getAttribute('value');
            
            // For a gicen session, get the same csrf _token
            echo sprintf("%s %s\n",$inputName,$inputValue);
        }
    }
}
function zayso_curl_get($url,$id = null)
{
    // create a new cURL resource
    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, get_option('zayso_host') . $url);
    
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Prevents auto echoing
  //curl_setopt($ch, CURLOPT_VERBOSE,        true); // Adds a bit more
    curl_setopt($ch, CURLOPT_HEADER,         true); // Gives me my Set-Cookie
    
    curl_setopt($ch, CURLOPT_COOKIE, zayso_load_cookies()); // Sample cookie
    
    // grab URL and pass it to the browser
    $response = curl_exec($ch);
    
    // close cURL resource, and free up system resources
    curl_close($ch);
    
    list($header, $body) = explode("\r\n\r\n", $response, 2);
    
    zayso_save_cookies($header);
 
    return zayso_extract_fragment($body,$id);
}
function zayso_curl_post($url,$post)
{
    // create a new cURL resource
    $ch = curl_init();

    // set URL and other appropriate options
    curl_setopt($ch, CURLOPT_URL, get_option('zayso_host') . $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // Prevents auto echoing
    curl_setopt($ch, CURLOPT_HEADER,         true); // Gives me my Set-Cookie
    curl_setopt($ch, CURLOPT_POST,           true);
    curl_setopt($ch, CURLOPT_COOKIE,zayso_load_cookies());
    
    // Need the build to handle array of arrays
    curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($post)); 

    // grab URL and pass it to the browser
    $response = curl_exec($ch);
    
    // close cURL resource, and free up system resources
    curl_close($ch);
echo $response; die('xxx');    
    list($header, $body) = explode("\r\n\r\n", $response, 2);
    if ($header == 'HTTP/1.1 100 Continue')
    {
        list($header, $body) = explode("\r\n\r\n", $body, 2);
    }
    zayso_save_cookies($header);
    
    return $body;
}
?>
