<?php

class Handler {
   public function  __construct(
           public string $pattern,
           public string|array $verb,
           public int   $type,
           public mixed $target,
           public int $ttl = 0,
           public int $kbps = 0,
           public ?string $alias = null
    ){}
           
    public function addVerb(string $verb) 
    {
        if (is_string($this->verb)) {
            $this->verb = [$this->verb, $verb];
        }
        else if (is_array($this->verb)) {
            $this->verb[] = $verb;
        }
    }
    
    public function hasVerb(string $verb) : bool
    {
        if (is_string($this->verb) && (strcasecmp($this->verb,$verb)===0)) {
            return true;
        }
        if (is_array($this->verb)) {
            foreach($this->verb as $v) {
                if (strcasecmp($v,$verb)===0)
                {
                    return true;
                }
            }
        }
    }
}
