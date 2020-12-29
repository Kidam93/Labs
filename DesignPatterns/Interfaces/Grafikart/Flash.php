<?php


namespace Grafikart;


class Flash {

    /**
     * @var Session
     */
    private $session;
    const KEY = 'gflash';

    public function __construct(SessionInterface $session){
        $this->session = $session;
    }

    public function set($message, $type = 'success'){
        $this->session->set(self::KEY, [
            'message' => $message,
            'type'    => $type
        ]);
    }

    public function get(){
        $flash = $this->session->get(self::KEY);
        $this->session->delete(self::KEY);
        return "<div class='alert alert-{$flash['type']}'>{$flash['message']}</div>";
    }

}