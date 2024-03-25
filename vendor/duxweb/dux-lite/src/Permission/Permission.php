<?php
declare(strict_types=1);

namespace Dux\Permission;

class Permission
{

    private array $data = [];
    private string $pattern;
    private string $app = '';
    public static array $actions = ['list', 'show', 'create', 'edit', 'store', 'delete'];

    public function __construct(string $pattern = "")
    {
        $this->pattern = $pattern;
    }

    public function setApp(string $app): void
    {
        $this->app = $app;
    }

    public function group(string $name, int $order = 0): PermissionGroup
    {
        $group = new PermissionGroup($this->app, $name, $order, $this->pattern);
        $this->data[] = $group;
        return $group;
    }


    public function resources(string $name, int $order = 0, array|false $actions = [], bool $softDelete = false): PermissionGroup
    {
        $group = $this->group($name, $order);

        if ($actions === false) {
            return $group;
        }

        if (!$actions) {
            $actions = self::$actions;
        }

        $actions = array_intersect(self::$actions, $actions);

        if ($softDelete) {
            $actions = [...$actions, 'trash', 'restore'];
        }

        foreach ($actions as $vo) {
            $group->add($vo);
        }

        return $group;
    }

    public function get(): array
    {
        $data = [];
        foreach ($this->data as $vo) {
            $data[] = $vo->get();
        }
        return collect($data)->sortBy("order")->toArray();
    }

    public function getData(): array
    {
        $data = [];
        foreach ($this->data as $vo) {
            $data = [...$data, ...$vo->getData()];
        }
        return $data;
    }

}