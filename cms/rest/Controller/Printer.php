<?php
namespace Controller;
class Printer {

    public function printJsonResponse($response, $echo = true) {
        header("Content-Type: application/json");
        $json_response = json_encode($response);
        if ($echo) {
            echo $json_response;
        }
        return $json_response;
    }


}
