<?php
declare(strict_types=1);

namespace Dux\Config;

use Exception;
use Noodlehaus\Exception\ParseException;
use Symfony\Component\Yaml\Tag\TaggedValue;
use Symfony\Component\Yaml\Yaml as YamlParser;

class Yaml extends \Noodlehaus\Parser\Yaml
{
    /**
     * @param $filename
     * @return array
     * @throws ParseException
     */
    public function parseFile($filename): array
    {
        if (!is_file($filename) || !is_readable($filename)) {
            throw new \Symfony\Component\Yaml\Exception\ParseException(sprintf('File "%s" does not exist or unreadable.', $filename));
        }
        $content = file_get_contents($filename);
        return $this->parseString($content);
    }

    /**
     * @param $config
     * @return array
     * @throws ParseException
     */
    public function parseString($config): array
    {
        foreach (Config::$variables as $key => $value) {
            $config = str_replace("%$key%", $value, $config);
        }

        try {
            $data = YamlParser::parse($config, YamlParser::PARSE_CONSTANT + YamlParser::PARSE_CUSTOM_TAGS);
        } catch (Exception $exception) {
            throw new ParseException(
                [
                    'message' => 'Error parsing YAML string',
                    'exception' => $exception,
                ]
            );
        }

        return $this->parse($data);
    }

    protected function parse($data = null): ?array
    {
        if (!$data) {
            return null;
        }
        return $this->parseValue($data);
    }

    protected function parseValue(array $data): array
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $this->parseValue($value);
            }
            if ($value instanceof TaggedValue) {
                $tag = $value->getTag();
                $fun = Config::getTag($tag);
                if (!$fun) {
                    $data[$key] = null;
                } else {
                    $params = $value->getValue() ?: [];
                    if (!is_array($params)) {
                        $params = [$params];
                    }
                    $data[$key] = call_user_func($fun, ...$params);
                }
            }
        }
        return $data;

    }
}
