<?php
declare(strict_types=1);

namespace Dux\Database;

use Doctrine\DBAL\Schema\Comparator;
use Dux\App;
use Dux\Database\Attribute\AutoMigrate;
use Illuminate\Database\Connection;
use Illuminate\Database\Schema\Blueprint;
use Symfony\Component\Console\Output\OutputInterface;

class Migrate
{
    public array $migrate = [];

    public function register(string ...$model): void
    {
        $this->migrate = [...$this->migrate, ...$model];
    }

    public function migrate(OutputInterface $output, string $name = ''): void
    {
        $name = ucfirst($name);
        $seeds = [];
        $connect = App::db()->getConnection();
        foreach ($this->migrate as $model) {
            if ($name && !str_contains($model, "\\$name\\Models\\")) {
                continue;
            }

            if (!method_exists($model, 'migration')) {
                continue;
            }
            $startTime = microtime(true);
            $modelObj = new $model;
            $this->migrateTable($connect, $modelObj, $seeds);

            if (method_exists($model, 'migrationAfter')) {
                $modelObj->migrationAfter($modelObj->getConnection());
            }

            $time = round(microtime(true) - $startTime, 3);
            $output?->writeln("sync model <info>$model</info> {$time}s");
        }

        foreach ($seeds as $seed) {
            $startTime = microtime(true);
            $seed->seed($connect);
            $time = round(microtime(true) - $startTime, 3);
            $name = $seed::class;
            $output?->writeln("sync send <info>$name</info> {$time}s");
        }

    }

    private function migrateTable(Connection $connect, Model $model, &$seed): void
    {
        $pre = $connect->getTablePrefix();
        $modelTable = $model->getTable();
        $tempTable = 'table_' . $modelTable;
        $tableExists = App::db()->getConnection()->getSchemaBuilder()->hasTable($modelTable);
        App::db()->getConnection()->getSchemaBuilder()->dropIfExists($tempTable);
        App::db()->getConnection()->getSchemaBuilder()->create($tableExists ? $tempTable : $modelTable, function (Blueprint $table) use ($model) {
            $model->migration($table);
            $model->migrationGlobal($table);
        });
        if (!$tableExists) {
            if (method_exists($model, 'seed')) {
                $seed[] = $model;
            }
            return;
        }
        // 更新表字段
        $manager = $model->getConnection()->getDoctrineSchemaManager();
        $modelTableDetails = $manager->introspectTable($pre . $modelTable);
        $tempTableDetails = $manager->introspectTable($pre . $tempTable);
        foreach ($tempTableDetails->getIndexes() as $indexName => $indexInfo) {
            $correctIndexName = str_replace('table_', '', $indexName);
            $tempTableDetails->renameIndex($indexName, $correctIndexName);
        }
        $diff = (new Comparator)->compareTables($modelTableDetails, $tempTableDetails);
        if ($diff) {
            $manager->alterTable($diff);
        }
        App::db()->getConnection()->getSchemaBuilder()->drop($tempTable);
    }


    // 注册迁移模型
    public function registerAttribute(): void
    {
        $attributes = (array)App::di()->get("attributes");
        foreach ($attributes as $attribute => $list) {
            if (
                $attribute !== AutoMigrate::class
            ) {
                continue;
            }
            foreach ($list as $vo) {
                $class = $vo["class"];
                $this->register($class);
            }
        }
    }

}