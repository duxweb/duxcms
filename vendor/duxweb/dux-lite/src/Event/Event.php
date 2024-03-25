<?php
declare(strict_types=1);

namespace Dux\Event;

use Dux\App;
use Dux\Event\Attribute\AppListener;
use Dux\Event\Attribute\Listener;
use RuntimeException;
use Symfony\Component\EventDispatcher\EventDispatcher;

class Event extends EventDispatcher
{

    public array $registers = [];
    private ?EventDataSource $source = null;

    public function __construct()
    {
        $status = App::config('use')->get('clock');
        if ($status) {
            $this->source = new EventDataSource($this);
            App::clock()->addDataSource($this->source);
        }
        parent::__construct();
    }

    public function addListener(string $eventName, callable|array $listener, int $priority = 0): void
    {
        $this->registers[$eventName][] = !is_array($listener) ? 'callable' : implode(':', [$listener[0]::class, $listener[1]]);
        parent::addListener($eventName, $listener, $priority);
    }

    public function dispatch(object $event, string $eventName = null): object
    {
        $startTime = microtime(true);
        $result = parent::dispatch($event, $eventName);
        if ($this->source) {
            $this->source->addEvent($event, $eventName, $startTime, microtime(true));
        }
        return $result;
    }

    public function registerAttribute(): void
    {
        $attributes = (array)App::di()->get("attributes");
        foreach ($attributes as $attribute => $list) {
            if (
                $attribute !== Listener::class && $attribute !== AppListener::class
            ) {
                continue;
            }
            $app = $attribute === AppListener::class;
            foreach ($list as $vo) {
                [$class, $method] = explode(':', $vo["class"]);
                $params = $vo["params"];

                if ($app) {
                    $name = $params["name"] . '.' . $params["class"];
                }else {
                    $name = $params["name"];
                }

                if ($app && (!$params["name"] || !$params["class"])) {
                    throw new RuntimeException("method [$class:$method] The annotation is missing the name or class parameter");
                }
                if (!$app && !$params["name"]) {
                    throw new RuntimeException("method [$class:$method] The annotation is missing the name parameter");
                }

                $this->addListener($name, [new $class, $method], (int)$params["priority"] ?: 0);
            }
        }
    }

}