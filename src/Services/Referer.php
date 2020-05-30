<?php
namespace App\Services;
trait Referer {
    private function getRefererParams() {
        $request = $this->request;
        $referer = $request->headers->get('referer');
        $baseUrl = $request->getBaseUrl();
        $lastPath = substr($referer, strpos($referer, $baseUrl) + strlen($baseUrl));
        return $this->get('router')->getMatcher()->match($lastPath);
    }
}
