<?php
namespace kontur\ajax;

use kontur\Ajax;

class Frontpad
{
    /**
     * Получить кол-во баллов клиента
     */
    public static function getFrontpadScore()
    {
        $phone = Ajax::getPost('phone');
        $response = ['success'=>false];
        
        if(!empty($phone)) {
            $score = \kontur\frontpad\FrontPad::getClientScore($phone);            
            if($score !== false) {
                $response = [
                    'success' => true, 
                    'score' => $score
                ];
            }
        }
        
        Ajax::sendResponse($response);
    }
    
    /**
     * Проверить сертификат
     */
    public static function getCertificate()
    {
        $certificate = Ajax::getPost('certificate');
        $response = ['success'=>false];
        
        if(!empty($certificate)) {
            $data = \kontur\frontpad\FrontPad::getCertificate($certificate);
            if(!empty($data)) {
                $response = $data;
                $response['success'] = ($data['result'] !== 'error');
            }
        }
        
        Ajax::sendResponse($response);
    }
}
