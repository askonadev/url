<?php
namespace Askonadev;

class Url
{
    private array $arParams = [];
    private array $arPathItems = [];
    
    public function __construct(string $sUrl)
    {
        $this->arPathItems = array_filter(explode("/", $sUrl));
    }
    
    public function addParam(string $sKey, $value): void
    {
        if (is_array($value)) {
            if (is_null($this->arParams[$sKey])) {
                $this->arParams[$sKey] = [];
            }
            $this->arParams[$sKey] = array_merge($this->arParams[$sKey], $value);
        } else {
            $this->arParams[$sKey] = $value;
        }
    }
    
    public function addPathItem(string $sPathItem): void
    {
        $this->arPathItems = array_merge($this->arPathItems, array_filter(explode("/", $sPathItem)));
    }
    
    public function removeParam(string $sKey): void
    {
        unset($this->arParams[$sKey]);
    }
    
    public function toString(): string
    {
        $sReturnUrl = $this->getPath();
        
        if (!empty($this->arParams)) {
            $sReturnUrl .= "?".str_replace(["%5B", "%5D"], ["[", "]"], http_build_query($this->arParams));
        }
        
        return $sReturnUrl;
    }
    
    private function getPath(): string
    {
        return "/".implode("/", $this->arPathItems)."/";
    }
}
