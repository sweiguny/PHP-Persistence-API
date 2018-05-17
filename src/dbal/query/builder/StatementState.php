<?php

namespace PPA\dbal\query\builder;

class StatementState
{
    const STATE_DIRTY = 2;
    const STATE_CLEAN = 1;
    
    /**
     *
     * @var int
     */
    private $state = self::STATE_CLEAN;
    
    /**
     * CollectionStateException code
     * 
     * @var int
     */
    private $code = 0;

    /**
     *
     * @var string
     */
    private $reason;

    /**
     *
     * @var array
     */
    private $allowedStates;
    
    public function __construct()
    {
        $reflector = new \ReflectionClass($this);
        $this->allowedStates = array_flip($reflector->getConstants());
//        var_dump($this->allowedStates);
    }
    
    public function getState(): int
    {
        return $this->state;
    }

    public function setState(int $state, int $code, ?string $reason)
    {
//        print_r(array_slice(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), 0, 4));
        
//        var_dump($this->allowedStates[$state]);
//        
//        if (!isset($this->allowedStates[$state]));
//        {
////            var_dump($state);
////            var_dump($this->allowedStates);
////            print_r($state);
////            print_r($this->allowedStates);
//            throw new \PPA\core\exceptions\logic\DomainException("State '{$state}' is not an allowed value.");
//        }
        
        $this->state  = $state;
        $this->code   = $code;
        $this->reason = $reason;
    }

    public function setStateClean(): void
    {
        $this->setState(self::STATE_CLEAN, 0, null);
    }

    public function setStateDirty(int $code, string $reason): void
    {
        $this->setState(self::STATE_DIRTY, $code, $reason);
    }
    
    public function stateIsDirty(): bool
    {
        return $this->getState() == self::STATE_DIRTY;
    }
    
    public function stateIsClean(): bool
    {
        return $this->getState() == self::STATE_CLEAN;
    }
    
    public function getReason(): ?string
    {
        return $this->reason;
    }
    
    public function getCode(): int
    {
        return $this->code;
    }

}

?>
