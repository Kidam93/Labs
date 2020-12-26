<?php
namespace Event;

class Listener {

    /**
     * @var callable
     */
    public $callback;

    /**
     * @var int
     */
    public $priority;

    /**
     * Définie si le listener peut être appellé plusieurs fois
     *
     * @var bool
     */
    private $once = false;

    /**
     * Permet de stopper les évènements parents
     *
     * @var bool
     */
    public $stopPropagation = false;

    /**
     * Permet de savoir combien de fois le listener a été appellé
     * @var bool
     */
    private $calls = 0;

    public function __construct(callable $callback, int $priority)
    {
        $this->callback = $callback;
        $this->priority = $priority;
    }

    public function handle(array $args)
    {
        if ($this->once && $this->calls > 0) {
            return null;
        }
        $this->calls++;
        return call_user_func_array($this->callback, $args);
    }

    /**
     * Permet d'indiquer que le listener ne peut être appellé qu'une fois
     *
     * @return Listener
     */
    public function once (): Listener {
        $this->once = true;
        return $this;
    }

    /**
     * Permet de stopper l'éxécution des évènements suivant
     *
     * @return Listener
     */
    public function stopPropagation (): Listener {
        $this->stopPropagation = true;
        return $this;
    }

}