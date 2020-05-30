<?php
namespace App\Twig;

use App\Entity\NewsNotification;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;

class AppExtentions extends AbstractExtension 
{
    // private $message;
    public function __construct()
    {
        
    }

    public function getFilters()
    {
        return [
            new TwigFilter('price',[$this,'priceFilter']),
        
            new TwigFilter('str',[$this,'strFilter']),
        ];
    }
    public function strFilter($content, $length){
          
        return substr($content,0,$length);
    }

    public function priceFilter($number){
        return '$'.number_format($number,2,'.',',');
    }

    public function getTests()
    {
        return [
            
          
              new \Twig\TwigTest('news',function($obj){
                return $obj instanceof NewsNotification;
              })


        ];
    }

}