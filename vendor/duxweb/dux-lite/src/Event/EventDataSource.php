<?php

namespace Dux\Event;

use Clockwork\DataSource\DataSource;
use Clockwork\Helpers\Serializer;
use Clockwork\Helpers\StackTrace;
use Clockwork\Request\Request;
use Closure;
use Dux\App;
use ReflectionFunction;

class EventDataSource extends DataSource
{
    // Fired events
    protected array $events = [];
    private Event $dispatcher;

    public function __construct(Event $dispatcher)
    {
        $this->dispatcher = $dispatcher;

    }

    public function resolve(Request $request)
    {
        $request->events = array_merge($request->events, $this->events);
        return $request;
    }

    public function reset()
    {
        $this->events = [];
    }


    public function addEvent(object $event, string $name = null, float $startTime = 0, float $stopTime = 0)
    {
        $trace = StackTrace::get()->resolveViewName();

        $event = [
            'event' => $name ? $name . " [" . $event::class . "]" : $event::class,
            'data' => [],
            'duration' => ($stopTime - $startTime) / 1000,
            'listeners' => $this->findListenersFor($name ?: $event::class),
            'trace' => (new Serializer)->trace($trace)
        ];

        if ($this->passesFilters([$event])) {
            $this->events[] = $event;
        }
    }

    private function findListenersFor(string $event): array
    {
        App::log()->debug($event);
        $data = array_map(function ($listener) {

            if (is_array($listener) && count($listener) == 2) {
                if (is_object($listener[0])) {
                    return get_class($listener[0]) . '@' . $listener[1];
                } else {
                    return $listener[0] . '::' . $listener[1];
                }
            } elseif ($listener instanceof Closure) {
                $listener = new ReflectionFunction($listener);
                $filename = str_replace(base_path(), '', $listener->getFileName());
                $startLine = $listener->getStartLine();
                $endLine = $listener->getEndLine();
                return "Closure ({$filename}:{$startLine}-{$endLine})";
            }
            return null;
        }, $this->dispatcher->getListeners($event));

        return array_filter($data);
    }
}