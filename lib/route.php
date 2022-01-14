<?php

class Route {
    const REQ_GET = 1;
    const REQ_POST = 2;
    const REQ_PUT = 4;
    const REQ_PATCH = 8;
    const REQ_OPTIONS = 16;
    const REQ_DELETE = 32;
    const REQ_HEAD = 64;
    
    const NO_AJAX = 1;
    const ONLY_AJAX = 2;
    const AND_AJAX = 3;

    public static array $requestTypes = [
        "GET" => self::REQ_GET,
        "POST" => self::REQ_POST,
        "PUT" => self::REQ_PUT,
        "PATCH" => self::REQ_PATCH,
        "OPTIONS" => self::REQ_OPTIONS,
        "DELETE" => self::REQ_DELETE,
        "HEAD" => self::REQ_HEAD,
    ];
    public static array $reqTypeNames = [
        self::REQ_GET => "GET",
        self::REQ_POST => "POST",
        self::REQ_PUT => "PUT",
        self::REQ_PATCH => "PATCH",
        self::REQ_OPTIONS => "OPTIONS",
        self::REQ_DELETE => "DELETE",
        self::REQ_HEAD => "HEAD",
    ];
    
   public int $verbits = 0;
   
   public function  __construct(
           public string $pattern,
           public string|array $verb,
           public int   $type,
           public mixed $target,
           public int $ttl = 0,
           public int $kbps = 0,
           public ?string $alias = null
    )
    {
       $this->calcVerBits();
    }
           
    public function calcVerBits() {
        if (is_string($this->verb)) {
            $bits = self::$requestTypes[$this->verb];
        }
        else if (is_array($this->verb)){
            $bits = 0;
            foreach($this->verb as $v) {
                $flag = self::$requestTypes[$v] ?? 0;
                if (!$flag) {
                    self::badVerb($v);
                }
                $bits |= $flag;
            }
        }
        $this->verbits = $bits;
        
    }
    
    public static function badVerb(string $v) 
    {
        throw \Exception('Invalid Request Verb ' . $v);
    }
    public function setVerbs(string | array $verb)
    {
        $this->verb = $verb;
        $this->calcVerBits();
    }
    public function addVerb(string $verb) 
    {
        $v = strtoupper($verb);
        $flag = self::$requestTypes[$v] ?? 0;
        
        if (!$flag) {
            self::badVerb($verb);
        }
       
        if ($this->verbits & $flag !== 0) {
            return;
        }
        if (is_string($this->verb)) {
            $this->verb = [$this->verb, $v];
        }
        else if (is_array($this->verb)) {
            $this->verb[] = $v;
        }
        else {
            $this->verb = $v;
        }
        $this->verbits |= $flag;
    }
    
    public function hasVerb(string $verb) : bool
    {
        $v = strtoupper($verb);
        $flag = self::$requestTypes[$v] ?? 0;
        return ($this->verbits & $flag !== 0);
    }
}
