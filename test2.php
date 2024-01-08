<?php

$path = "C:/wamp64/www/public/winback/public/Ressource/package/BACK4/WLE256_12_2_v000.047.bin";
function getContent($path){
    if ($content = file_get_contents($path)) {
        return $content;
    } else {
        echo "\r\nfile is too big. Content cannot be get.\r\n";
        return false;
    }
}

function setFileContent(string $fileContent, $fromIndex = 0, $startOffset = 0){
    $startOffset += $fromIndex;
    //$fileContentFromIndex = $this->getContentFromIndex($fileContent, $startOffset); // TODO check startOffset et fromIndex
    $fileContentSubstr = substr($fileContent, $startOffset, $length = 256);
    return $fileContentSubstr;
}

function file_get_contents_chunked($file,$chunk_size,$callback)
{
    try
    {
        $handle = fopen($file, "r");
        $i = 0;
        while (!feof($handle))
        {
            call_user_func_array($callback,array(fread($handle,$chunk_size),&$handle,$i));
            $i++;
        }
        fclose($handle);
    }
    catch(Exception $e)
    {
         trigger_error("file_get_contents_chunked::" . $e->getMessage(),E_USER_NOTICE);
         return false;
    }
    return true;
}

//$filename = $path;
//$chunkSize = FW_OCTETS; // Adjust the chunk size as needed

function getChunk($filename, $chunkSize) {
    $fileHandle = fopen($filename, 'rb');

    if ($fileHandle === false) {
        die('Unable to open file.');
    }
    
    while (!feof($fileHandle)) {
        $chunk = fread($fileHandle, $chunkSize);
    
        // Process or output the chunk as needed
        // For example, you can echo it, save to another file, etc.
        echo strlen($chunk);
        
        // You can also perform operations on the chunk before processing
        // For example, processChunk($chunk);
    
        // Flush the output to avoid memory issues with large files
        //ob_flush();
        flush();
        return $chunk;
    }
    
    fclose($fileHandle);
}


/*
$success = file_get_contents_chunked($path, 4096, function($chunk,&$handle,$iteration){

    
});
*/
/*
$fileContent = setFileContent(getContent($path));
//$fileContentTest = file_get_contents_chunked($path, FW_OCTETS, );
$fileContentTest = getChunk($path, 256);

if ($fileContent == $fileContentTest) {
    print_r("files are similar");
}
*/
echo hex2bin("AA");
echo hexdec("AA");