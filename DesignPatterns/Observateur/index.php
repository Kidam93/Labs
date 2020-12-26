<?php
require 'vendor/autoload.php';

$emitter->emit('Comment.created', $comment);
$emitter->emit('User.new', $user);

$emitter->on('User.new', function($user){
    // mail(....);
});

$emitter->on('User.new', function($user){
    // mail(....);
});