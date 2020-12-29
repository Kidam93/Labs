<?php
require 'vendor/autoload.php';

$manager = new \Grafikart\EventManager();

// On va écouter des évènements
$manager->attach('database.delete.post', function (\Grafikart\DeletePostEvent $event) use ($manager) {
    unlink($event->getTarget()->getImage());
});
$manager->attach('upload.image', function (\Grafikart\DeletePostEvent $event) use ($manager) {
    unlink($event->getTarget()->getImage());
});


// Dans notre code
$post = new \Grafikart\Post();
$manager->trigger(new \Grafikart\DeletePostEvent($post));
