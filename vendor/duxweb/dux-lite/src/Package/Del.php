<?php

namespace Dux\Package;

use Illuminate\Support\Collection;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class Del
{
    public static function main(OutputInterface $output, array $appPackages): void
    {
        $configFile = base_path('app.json');
        $configLockFile = base_path('app.lock');
        $appJson = [];
        $appLockJson = [];
        if (is_file($configFile)) {
            $appJson = Package::getJson($configFile);
        }
        if (is_file($configLockFile)) {
            $appLockJson = Package::getJson($configLockFile);
        }

        $dependencies = collect($appJson['dependencies'] ?: []);
        $packages = collect($appLockJson['packages'] ?: []);

        $apps = [];
        $files = [];
        $phpDependencies = [];
        $jsDependencies = [];
        foreach ($appPackages as $packageName) {

            // 查找当前包
            $package = $packages->firstWhere('name', $packageName);
            if (!$package) {
                $output->writeln('Did you find the installation package ' . $packageName);
                continue;
            }

            // 依赖提醒
            $dependentPackages = $packages->filter(function ($package) use ($packageName) {
                return $package['dependencies'] && in_array($packageName, array_keys($package['dependencies']));
            });
            if (!$dependentPackages->isEmpty()) {
                $output->writeln('<fg=red>Error: Cannot uninstall ' . $packageName . '. It is depended upon by:</>');
                foreach ($dependentPackages as $dependentPackage) {
                    $output->writeln(' - ' . $dependentPackage['name']);
                }
                return;
            }

            // 过滤依赖
            $dependencies = $dependencies->except($package['name']);

            // 过滤包
            $packages = $packages->filter(function ($item) use ($package) {
                if ($item['name'] == $package['name']) {
                    return false;
                }
                return true;
            });

            // 建立文件索引
            $app = $package['app'];
            $appPath = app_path(ucfirst($app));
            $jsPath = base_path('web/src/pages/' . $app);
            $configPath = config_path($app . '.yaml');

            if (is_dir($appPath)) {
                $files[] = $appPath;
            }
            if (is_dir($jsPath)) {
                $files[] = $jsPath;
            }
            if (is_file($configPath)) {
                $files[] = $configPath;
            }
            $apps[] = $app;

            $phpDependencies = [...$phpDependencies, ...$package['phpDependencies'] ?: []];
            $jsDependencies = [...$jsDependencies, ...$package['jsDependencies'] ?: []];
        }

        $appJson['dependencies'] = $dependencies->toArray();
        $appLockJson['packages'] = $packages->toArray();


        $filteredPhpDeps = self::filterDependencies('phpDependencies', array_filter($phpDependencies), $packages);
        $filteredJsDeps = self::filterDependencies('jsDependencies', array_filter($jsDependencies), $packages);

        Package::del($output, $files);
        Package::saveConfig($output, $apps, true);
        Package::saveJson($configFile, $appJson);
        Package::saveJson($configLockFile, $appLockJson);
        Package::composer($output, $filteredPhpDeps, true);
        Package::node($output, $filteredJsDeps, true);

        $output->writeln('<info>Add Application Success</info>');
    }

    private static function filterDependencies(string $name, array $currentDeps, Collection $allPackages): array
    {
        foreach ($allPackages as $pkg) {
            if (isset($pkg[$name])) {
                foreach ($currentDeps as $dep => $version) {
                    if (isset($pkg[$name][$dep])) {
                        unset($currentDeps[$dep]);
                    }
                }
            }
        }
        return $currentDeps;
    }


}