<?php
declare(strict_types=1);

namespace Dux\Permission;

use Dux\App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Helper\TableCell;
use Symfony\Component\Console\Helper\TableSeparator;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class PermissionCommand extends Command
{

    protected static $defaultName = 'permission';
    protected static $defaultDescription = 'show permission list';


    protected function configure(): void
    {
        $this->addArgument(
            'group',
            InputArgument::OPTIONAL,
            'Who do you want to greet (separate multiple names with a space)?'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {

        $group = $input->getArgument("group");
        if ($group) {
            $permissionList = [$group => App::$bootstrap->permission->get($group)];
        } else {
            $permissionList = App::$bootstrap->permission->app;
        }

        foreach ($permissionList as $key => $item) {
            $data = [];
            $permissions = $item->get();
            foreach ($permissions as $k => $permission) {
                if ($k) {
                    $data[] = new TableSeparator();
                }
                $data[] = [$permission["label"]];
                foreach ($permission["children"] as $vo) {
                    $data[] = [$vo["label"], $vo["name"]];

                }
            }
            $table = new Table($output);
            $table
                ->setHeaders([
                    [new TableCell("permissions {$key}", ['colspan' => 2])],
                    ['Label', 'Name']
                ])
                ->setRows($data);
            $table->render();
        }


        return Command::SUCCESS;
    }
}