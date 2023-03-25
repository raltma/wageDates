<?php 

namespace spinTek\objects;
class Error {
    public static function showJsonError(string $errorMessage, int $http_code = 400){
        $messageArray=[
            'errorMessage' => $errorMessage
        ];
        echo json_encode($messageArray);
        http_response_code($http_code);
        exit;
    }
    
}