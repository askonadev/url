<?php

namespace Askonadev;

class Url
{
    private string $scheme   = "";
    private string $host     = "";
    private string $fragment = "";
    private array  $arParams = [];
    private array  $arPathItems = [];
    private bool   $isLocal;

    private bool $bClosePathSlash = true;

    public function __construct(?string $sUrl = "")
    {
        $arParsedUrl = parse_url($sUrl);

        $this->setPathItems($arParsedUrl["path"]);

        if (!empty($arParsedUrl["query"])) {
            $this->parseQuery($arParsedUrl["query"]);
        }

        if (!empty($arParsedUrl["scheme"])) {
            $this->setScheme($arParsedUrl["scheme"]);
        }

        if (!empty($arParsedUrl["host"])) {
            $this->setHost($arParsedUrl["host"]);
        }

        if (!empty($arParsedUrl["fragment"])) {
            $this->setFragment($arParsedUrl["fragment"]);
        }
    }

    private function setPathItems(?string $path = ""): void
    {
        if (!empty($this->arPathItems)) {
            $this->arPathItems = array_merge($this->arPathItems, array_filter(explode("/", $path), "strlen"));
        } else {
            $this->setIsLocal($path);
            $this->arPathItems = array_filter(explode("/", $path), "strlen");
        }
    }

    private function setIsLocal(?string $path = ""): void
    {
        $this->isLocal = strpos($path, "/") !== 0;
    }

    public function setClosePathSlash(bool $bClosePathSlash): void
    {
        $this->bClosePathSlash = $bClosePathSlash;
    }

    public function getClosePathSlash(): bool
    {
        return $this->bClosePathSlash;
    }

    public function parseQuery(string $sQuery): void
    {
        $data = preg_replace_callback(
            "/(?:^|(?<=&))[^=[]+/",
            function ($match) {
                return bin2hex(urldecode($match[0]));
            },
            $sQuery
        );

        parse_str($data, $values);

        foreach (array_combine(array_map("hex2bin", array_keys($values)), $values) as $sKey => $arValue) {
            $this->addParam($sKey, $arValue);
        }
    }

    public function setPath(string $sUrl): void
    {
        $this->setPathItems($sUrl);
    }

    public function addPathItem(?string $sUrl = ""): void
    {
        $this->setPathItems($sUrl);
    }

    public function setScheme(string $scheme): void
    {
        $this->scheme = str_replace("/", "", $scheme);
    }

    public function getScheme(): string
    {
        return $this->scheme;
    }

    public function setHost(string $host): void
    {
        $this->host = str_replace("/", "", $host);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setFragment(string $fragment): void
    {
        $this->fragment = $fragment;
    }

    public function getFragment(): string
    {
        return $this->fragment;
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
        $sReturnUrl = "";

        if (!empty($this->getHost())) {
            if (!empty($this->getScheme())) {
                $sReturnUrl .= $this->getScheme()."://";
            }

            $sReturnUrl .= $this->getHost();
        }

        $sReturnUrl .= $this->getPath();

        if (!empty($this->arParams)) {
            $sReturnUrl .= "?".str_replace(["%5B", "%5D"], ["[", "]"], http_build_query($this->arParams));
        }

        if (!empty($this->getFragment())) {
            $sReturnUrl .= "#".$this->getFragment();
        }

        return $sReturnUrl;
    }

    private function getPath(): string
    {
        if ($this->isLocal) {
            $sPath = "";
        } else {
            $sPath = "/";
        }

        if (!empty($this->arPathItems)) {
            $sPath .= implode("/", $this->arPathItems);

            if ($this->getClosePathSlash()) {
                $sPath .= "/";
            }
        }

        return $sPath;
    }
}
