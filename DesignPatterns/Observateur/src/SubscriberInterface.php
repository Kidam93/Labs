<?php
namespace Event;

interface SubscriberInterface {

    public function getEvents(): array;

}