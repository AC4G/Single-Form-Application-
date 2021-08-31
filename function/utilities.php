<?php


function isPost(): bool
{
    return strtoupper($_SERVER['REQUEST_METHOD']) === 'POST';
}

function getRandomHash(int $length): string
{
    $randomInt = random_int(0, time());
    $hash = md5($randomInt);
    $start = random_int(0, strlen($hash) - $length);
    $hashShort = substr($hash, $start, $length);
    return $hashShort;
}

function sendMail(Swift_Message $message): bool
{
    $transport = new Swift_SmtpTransport(SMTP_HOST, SMPT_PORT, SMTP_SSL);
    $transport->setUsername(SMTP_USERNAME);
    $transport->setPassword(SMTP_PASSWORD);

    $mailer = new Swift_Mailer($transport);
    return $mailer->send($message);
}

function logData(string $level, string $message, ?array $data = null)
{
    $today = date('Y-m-d');
    $now = date('Y-m-d H:i:s');
    if (!is_dir(LOG_DIR)) {
        mkdir(LOG_DIR, 0777, true);
    }
    $logFile = LOG_DIR . '/log-' . $today . '.log';

    $logData = '[' . $now . '-' . $level . '] ' . $message . "\n";

    if ($data) {
        $dataString = print_r($data, true) . "\n";
        $logData .= $dataString;
    }
   
    file_put_contents($logFile, $logData, FILE_APPEND);
}

function logEnd($string = '*')
{
    logData('INFO',str_repeat($string,100));
}

function normalizeFiles(array $files): array
{
    $result = [];
  
    foreach ($files as $keyName => $values) {
        foreach ($values as $index => $value) {
            $result[$index][$keyName] = $value;
        }
    }
   
    $typeToExtensionMap = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png'
    ];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    foreach ($result as $index => $file) {
        $tempPath = $file['tmp_name'];
        if(!$tempPath){
            unset($result[$index]);
            continue;
        }
        $type = finfo_file($finfo, $tempPath);
        $result[$index]['type'] = $type;
        $result[$index]['size'] = filesize($tempPath);
        if (isset($typeToExtensionMap[$type])) {
            $result[$index]['extension'] = $typeToExtensionMap[$type];
        }
    }

    return $result;
}

function router($path = null, $action = null, $methods = 'POST|GET',bool $directRequestDisabled = false) {
    static $routes = [];
    
    if(!$path){
        return $routes;
    }
    if(strpos($path, '..') !== false){
        return;
    }
    
    if ($action) {
        return $routes['(' . $methods . ')_' . $path] = [$action,$directRequestDisabled];
    }
    $originalPath = str_replace('?'.$_SERVER['QUERY_STRING'], '', $path);
    $path = $_SERVER['REQUEST_METHOD'].'_'.$originalPath;
    foreach ($routes as $route => $data) {
        list($action,$currentDirectRequestIsDisabled) = $data;
        $regEx = "~^$route/?$~i";
       
        $matches = [];
        if (!preg_match($regEx, $path, $matches)) {
            continue;
        }
        if (!is_callable($action)) {
       
            logData('WARNING','Route not found',['route'=>$path]);
            return false;
            
        }
        if($currentDirectRequestIsDisabled && $_SERVER['REQUEST_URI'] && $_SERVER['REQUEST_URI'] === $originalPath){
           
            logData('WARNING','Route not found',['route'=>$path]);
            return false;
        }
        array_shift($matches);
        array_shift($matches);
        $response = $action(...$matches);
        return $response;
    }
  
    logData('WARNING','Route not found',['route'=>$path]);
    return false;
}