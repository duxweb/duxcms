<?php
declare(strict_types=1);

namespace Dux\Route;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RouteCommand extends Command
{

    protected static $defaultName = 'route';
    protected static $defaultDescription = 'show route list';


    protected function configure(): void
    {
        $this->addArgument(
            'group',
            InputArgument::OPTIONAL,
            'please enter the route group name'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {

        $group = $input->getArgument("group");
        if ($group) {
            $routeList = [$group => App::$bootstrap->route->get($group)];
        } else {
            $routeList = App::$bootstrap->route->app;
        }

        foreach ($routeList as $key => $item) {
            $data = [];
            $routes = $item->parseData();
            foreach ($routes as $k => $route) {
                if ($k) {
                    $data[] = new TableSeparator();
                }
                $data[] = [$route["pattern"], $route["name"], is_array($route["methods"]) ? implode("|", $route["methods"]) : $route["methods"], $route["middleware"] ? implode("\n", $route["middleware"]) : "NULL"];
            }
            $table = new Table($output);
            $table
                ->setHeaders([
                    [new TableCell("routes {$key}", ['colspan' => 3])],
                    ['Pattern', 'Name', 'Methods', 'middleware']
                ])
                ->setRows($data);
            $table->render();
        }


        return Command::SUCCESS;
    }
}