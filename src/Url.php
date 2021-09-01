<?php
namespace Askonadev;

class Url
{
    private string $sPath;
    private array  $arParams = [];
    
    public function __construct(string $sUrl)
    {
        $this->sPath = $sUrl;
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
    
    public function removeParam(string $sKey): void
    {
        unset($this->arParams[$sKey]);
    }
    
    public function toString(): string
    {
        $sReturnUrl = $this->sPath;
        if (!empty($this->arParams)) {
            $sReturnUrl .= "?".str_replace(["%5B", "%5D"], ["[", "]"], http_build_query($this->arParams));
        }
        
        return $sReturnUrl;
    }
    
}
