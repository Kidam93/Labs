<?php
use Event\Emitter;
use Kahlan\Plugin\Double;

class FakeSubscriber implements \Event\SubscriberInterface {

    public function getEvents(): array
    {
        return [
            'Comment.created' => 'onNewComment',
            'Post.created'    => 'onNewPost'
        ];
    }
}

describe(Emitter::class, function () {

    beforeEach(function () {
       $reflection = new ReflectionClass(Emitter::class);
       $instance = $reflection->getProperty('_instance');
       $instance->setAccessible(true);
       $instance->setValue(null, null);
       $instance->setAccessible(false);
    });

    given('emitter', function () { return Emitter::getInstance(); });

    it('should be a singleton', function () {
       $instance = Emitter::getInstance();
       expect($instance)->toBeAnInstanceOf(Emitter::class);
       expect($instance)->toBe(Emitter::getInstance());
    });

    describe('::on', function () {

        it('should trigger the listened event', function () {
            $listener = Double::instance();
            $comment = ['name' => 'John'];

            expect($listener)->toReceive('onNewComment')->times(2)->with($comment);

            $this->emitter->on('Comment.created', [$listener, 'onNewComment']);
            $this->emitter->emit('Comment.created', $comment);
            $this->emitter->emit('Comment.created', $comment);
        });

        it('should trigger events in the right order', function () {
            $listener = Double::instance();

            expect($listener)->toReceive('onNewComment2')->once()->ordered;
            expect($listener)->toReceive('onNewComment')->once()->ordered;

            $this->emitter->on('Comment.created', [$listener, 'onNewComment'], 1);
            $this->emitter->on('Comment.created', [$listener, 'onNewComment2'], 200);
            $this->emitter->emit('Comment.created');
        });

        it('should run the first event first', function () {
            $listener = Double::instance();

            expect($listener)->toReceive('onNewComment')->once()->ordered;
            expect($listener)->toReceive('onNewComment2')->once()->ordered;

            $this->emitter->on('Comment.created', [$listener, 'onNewComment']);
            $this->emitter->on('Comment.created', [$listener, 'onNewComment2']);
            $this->emitter->emit('Comment.created');
        });

        it('should prevent the same listener twice', function () {
            $listener = Double::instance();
            $closure = function () use($listener) {
                $this->emitter->on('Comment.created', [$listener, 'onNewComment']);
                $this->emitter->on('Comment.created', [$listener, 'onNewComment']);
            };
            expect($closure)->toThrow(new \Event\DoubleEventException());
        });

    });

    describe('::once', function () {

        it('should trigger events once', function () {
            $listener = Double::instance();
            $comment = ['name' => 'John'];

            expect($listener)->toReceive('onNewComment')->once()->with($comment);

            $this->emitter->on('Comment.created', [$listener, 'onNewComment'])->once();
            $this->emitter->emit('Comment.created', $comment);
            $this->emitter->emit('Comment.created', $comment);
        });

    });

    describe('::stopPropagation', function () {

        it('should stop next listener', function () {
            $listener = Double::instance();

            expect($listener)->toReceive('onNewComment')->once();
            expect($listener)->not->toReceive('onNewComment2')->once();

            $this->emitter->on('Comment.created', [$listener, 'onNewComment'])->stopPropagation();
            $this->emitter->on('Comment.created', [$listener, 'onNewComment2']);
            $this->emitter->emit('Comment.created');
        });

    });

    describe('::addSubscriber', function () {

        it('should trigger every events', function () {
            $subscriber = Double::instance([
                'extends' => FakeSubscriber::class,
                'methods' => ['onNewComment', 'onNewPost']
            ]);
            $comment = ['name' => 'John'];

            expect($subscriber)->toReceive('onNewComment')->times(2)->with($comment);
            expect($subscriber)->toReceive('onNewPost')->once()->with(200, 300, 400);

            $this->emitter->addSubscriber($subscriber);
            $this->emitter->emit('Comment.created', $comment);
            $this->emitter->emit('Comment.created', $comment);
            $this->emitter->emit('Post.created', 200, 300, 400);
        });

    });

});