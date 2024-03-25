<?php

declare(strict_types=1);

/**
 * This code checks if the class 'ParsedownExtra' exists. If it does, it creates an alias for it called 'ParsedownTocParentAlias'.
 * If 'ParsedownExtra' does not exist, it creates an alias for 'Parsedown' called 'ParsedownTocParentAlias'.
 */

if (class_exists('ParsedownExtra')) {
    class_alias('ParsedownExtra', 'ParsedownTocParentAlias');
} else {
    class_alias('Parsedown', 'ParsedownTocParentAlias');
}

class ParsedownToc extends ParsedownTocParentAlias
{
    public const VERSION = '1.5.3';
    public const VERSION_PARSEDOWN_REQUIRED = '1.7.4';
    public const VERSION_PARSEDOWN_EXTRA_REQUIRED = '0.8.1';
    public const MIN_PHP_VERSION = '7.4';

    protected array $options = [];
    protected array $defaultOptions = array(
        'selectors' => ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'],
        'delimiter' => '-',
        'limit' => null,
        'lowercase' => true,
        'replacements' => null,
        'transliterate' => true,
        'urlencode' => false,
        'blacklist' => [],
        'url' => '',
        'toc_tag' => '[toc]',
        'toc_id' => 'toc',
    );

    private array $anchorDuplicates = [];
    private array $contentsListArray = [];
    private string $contentsListString = '';
    private $createAnchorIDCallback = null;
    private int $firstHeadLevel = 0;


    public function __construct()
    {

        // Check if PHP version is supported
        if (version_compare(PHP_VERSION, self::MIN_PHP_VERSION) < 0) {
            $msg_error  = 'Version Error.' . PHP_EOL;
            $msg_error .= '  ParsedownToc requires PHP version ' . self::MIN_PHP_VERSION . ' or later.' . PHP_EOL;
            $msg_error .= '  - Current version : ' . PHP_VERSION . PHP_EOL;
            $msg_error .= '  - Required version: ' . self::MIN_PHP_VERSION . PHP_EOL;
            throw new Exception($msg_error);
        }

        // Check if Parsedown version is supported
        if (version_compare(\Parsedown::version, self::VERSION_PARSEDOWN_REQUIRED) < 0) {
            $msg_error  = 'Version Error.' . PHP_EOL;
            $msg_error .= '  ParsedownToc requires a later version of Parsedown.' . PHP_EOL;
            $msg_error .= '  - Current version : ' . \Parsedown::version . PHP_EOL;
            $msg_error .= '  - Required version: ' . self::VERSION_PARSEDOWN_REQUIRED . ' and later' . PHP_EOL;
            throw new Exception($msg_error);
        }

        # If ParsedownExtra is installed, check its version
        if (class_exists('ParsedownExtra')) {
            if (version_compare(\ParsedownExtra::version, self::VERSION_PARSEDOWN_EXTRA_REQUIRED) < 0) {
                $msg_error  = 'Version Error.' . PHP_EOL;
                $msg_error .= '  ParsedownToc requires a later version of ParsedownExtra.' . PHP_EOL;
                $msg_error .= '  - Current version : ' . \ParsedownExtra::version . PHP_EOL;
                $msg_error .= '  - Required version: ' . self::VERSION_PARSEDOWN_EXTRA_REQUIRED . ' and later' . PHP_EOL;
                throw new Exception($msg_error);
            }
            
            /** @psalm-suppress DirectConstructorCall */
            parent::__construct();
        }

        // Initialize default options
        $this->options = $this->defaultOptions;
    }

    /**
     * Set options for the ParsedownToc parser.
     *
     * @param array $options The options to set.
     * @return void
     */
    public function setOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Set the selectors option.
     *
     * @param array $selectors The selectors to set.
     * @return void
     */
    public function setTocSelectors(array $selectors): void
    {
        $this->options['selectors'] = $selectors;
    }

    /**
     * Set the delimiter option.
     *
     * @param string $delimiter The delimiter to set.
     * @return void
     */
    public function setTocDelimiter(string $delimiter): void
    {
        $this->options['delimiter'] = $delimiter;
    }

    /**
     * Set the limit option.
     *
     * @param int|null $limit The limit to set.
     * @return void
     */
    public function setTocLimit(?int $limit): void
    {
        $this->options['limit'] = $limit;
    }

    /**
     * Set the lowercase option.
     *
     * @param bool $lowercase The lowercase option to set.
     * @return void
     */
    public function setTocLowercase(bool $lowercase): void
    {
        $this->options['lowercase'] = $lowercase;
    }

    /**
     * Set the replacements option.
     *
     * @param array|null $replacements The replacements to set.
     * @return void
     */
    public function setTocReplacements(?array $replacements): void
    {
        $this->options['replacements'] = $replacements;
    }

    /**
     * Set the transliterate option.
     *
     * @param bool $transliterate The transliterate option to set.
     * @return void
     */
    public function setTocTransliterate(bool $transliterate): void
    {
        $this->options['transliterate'] = $transliterate;
    }

    /**
     * Set the urlencode option.
     *
     * @param bool $urlencode The urlencode option to set.
     * @return void
     */
    public function setTocUrlencode(bool $urlencode): void
    {
        $this->options['urlencode'] = $urlencode;
    }

    /**
     * Set the blacklist option.
     *
     * @param array $blacklist The blacklist to set.
     * @return void
     */
    public function setTocBlacklist(array $blacklist): void
    {
        $this->options['blacklist'] = $blacklist;
    }

    /**
     * Set the url option.
     *
     * @param string $url The url to set.
     * @return void
     */
    public function setTocUrl(string $url): void
    {
        $this->options['url'] = $url;
    }

    /**
     * Set the toc_tag option.
     *
     * @param string $toc_tag The toc_tag to set.
     * @return void
     */
    public function setTocTag(string $toc_tag): void
    {
        $this->options['toc_tag'] = $toc_tag;
    }

    /**
     * Set the toc_id option.
     *
     * @param string $toc_id The toc_id to set.
     * @return void
     */
    public function setTocId(string $toc_id): void
    {
        $this->options['toc_id'] = $toc_id;
    }

    /**
     * Heading process.
     * Creates heading block element and stores to the ToC list. It overrides
     * the parent method: \Parsedown::blockHeader() and returns $Block array if
     * the $Line is a heading element.
     *
     * @param  array $Line  Array that Parsedown detected as a block type element.
     * @return void|array   Array of Heading Block.
     */
    protected function blockHeader($Line)
    {
        // Use parent blockHeader method to process the $Line to $Block
        $Block = parent::blockHeader($Line);

        if (!empty($Block)) {
            $text = $Block['element']['text'] ?? $Block['element']['handler']['argument'] ?? '';
            $level = $Block['element']['name'];
            $id = $Block['element']['attributes']['id'] ?? $this->createAnchorID($text);

            $Block['element']['attributes'] = ['id' => $id];

            // Check if heading level is in the selectors
            if (!in_array($level, $this->options['selectors'])) {
                return $Block;
            }

            $this->setContentsList(['text' => $text, 'id' => $id, 'level' => $level]);

            return $Block;
        }
    }

    /**
     * Heading process.
     * Creates heading block element and stores to the ToC list. It overrides
     * the parent method: \Parsedown::blockSetextHeader() and returns $Block array if
     * the $Line is a heading element.
     *
     * @param  array $Line Array that Parsedown detected as a block type element.
     * @return void|array Array of Heading Block.
     */
    protected function blockSetextHeader($Line, array $Block = null)
    {
        // Use parent blockHeader method to process the $Line to $Block
        $Block = parent::blockSetextHeader($Line, $Block);

        if (!empty($Block)) {
            $text = $Block['element']['text'] ?? $Block['element']['handler']['argument'] ?? '';
            $level = $Block['element']['name'];
            $id = $Block['element']['attributes']['id'] ?? $this->createAnchorID($text);

            $Block['element']['attributes'] = ['id' => $id];

            // Check if heading level is in the selectors
            if (!in_array($level, $this->options['selectors'])) {
                return $Block;
            }

            $this->setContentsList(['text' => $text, 'id' => $id, 'level' => $level]);

            return $Block;
        }
    }

    /**
     * Parses the given markdown string to an HTML string but it leaves the ToC
     * tag as is. It's an alias of the parent method "\parent::text()".
     *
     * @param  string $text  Markdown string to be parsed.
     * @return string        Parsed HTML string.
     */
    public function body(string $text): string
    {
        $text = $this->encodeTagToHash($text);   // Escapes ToC tag temporary
        $html = parent::text($text);      // Parses the markdown text
        $html = $this->decodeTagFromHash($html); // Unescape the ToC tag

        return $html;
    }

    /**
     * Returns the parsed ToC.
     * If the arg is "string" then it returns the ToC in HTML string.
     *
     * @param  string $type_return Type of the return format. "string" or "json".
     * @return string HTML/JSON string of ToC.
     */
    public function contentsList(string $type_return = 'html')
    {
        switch (strtolower($type_return)) {
            case 'string':
            case 'html':
                return $this->contentsListString ? $this->body($this->contentsListString) : '';
            case 'json':
                return json_encode($this->contentsListArray);
            case 'array':
                return $this->contentsListArray;
            default:
                $backtrace = debug_backtrace();
                $caller = $backtrace[0];
                $errorMessage = "Unknown return type '{$type_return}' given while parsing ToC. Called in " . $caller['file'] . " on line " . $caller['line'];
                throw new InvalidArgumentException($errorMessage);
        }
    }


    /**
     * Allows users to define their own logic for createAnchorID.
     */
    public function setCreateAnchorIDCallback(callable $callback): void
    {
        $this->createAnchorIDCallback = $callback;
    }


    /**
     * Creates an anchor ID for the given text.
     *
     * If a callback is provided, it uses the user-defined logic to create the anchor ID.
     * Otherwise, it uses the default logic which involves normalizing the string, replacing characters, and sanitizing the anchor.
     *
     * @param  string $text The text for which to create the anchor ID.
     * @return string The created anchor ID.
     */
    protected function createAnchorID($text): string
    {
        // Use user-defined logic if a callback is provided
        if (is_callable($this->createAnchorIDCallback)) {
            return call_user_func($this->createAnchorIDCallback, $text, $this->options);
        }

        if ($this->options['urlencode']) {
            $text = urlencode($text);
            // Check AnchorID is unique
            return $this->uniquifyAnchorID($text);
        }

        // Lowercase the string
        $text = $this->options['lowercase'] ? mb_strtolower($text, 'UTF-8') : $text;

        // Make custom replacements
        if (!empty($this->options['replacements'])) {
            $text = preg_replace(array_keys($this->options['replacements']), $this->options['replacements'], $text);
        }

        // Remove non UTF-8 characters
        $text = $this->normalizeString($text);

        // Transliterate characters to ASCII
        if ($this->options['transliterate']) {
            $text = $this->transliterate($text);
        }

        // Sanitize the anchor
        $text = $this->sanitizeAnchor($text);

        // Truncate slug to max. characters
        $text = mb_substr($text, 0, ($this->options['limit'] ? $this->options['limit'] : mb_strlen($text, 'UTF-8')), 'UTF-8');

        // Check AnchorID is unique
        $text = $this->uniquifyAnchorID($text);

        return $text;
    }

    /**
     * Normalize a string by converting it to encoding it to UTF-8.
     *
     * @param string $text The string to be normalized.
     *
     * @return array|false|string
     */
    protected function normalizeString(string $text)
    {
        return mb_convert_encoding($text, 'UTF-8', mb_list_encodings());
    }

    /**
     * Replaces special characters in a string with their corresponding ASCII equivalents.
     *
     * @param  string $text The input string.
     * @return string The modified string with replaced characters.
     */
    protected function transliterate(string $text): string
    {
        $characterMap = [
            // Latin
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'AA', 'Æ' => 'AE', 'Ç' => 'C',
            'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
            'Ø' => 'OE', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
            'ß' => 'ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'aa', 'æ' => 'ae', 'ç' => 'c',
            'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
            'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
            'ø' => 'oe', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
            'ÿ' => 'y',

            // Latin symbols
            '©' => '(c)', '®' => '(r)', '™' => '(tm)',

            // Greek
            'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => 'TH',
            'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => 'X', 'Ο' => 'O', 'Π' => 'P',
            'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'O',
            'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'O', 'Ϊ' => 'I',
            'Ϋ' => 'Y',
            'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => 'th',
            'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => 'x', 'ο' => 'o', 'π' => 'p',
            'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'o',
            'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'o', 'ς' => 's',
            'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',

            // Turkish
            'Ş' => 'S', 'İ' => 'I', 'Ğ' => 'G',
            'ş' => 's', 'ı' => 'i', 'ğ' => 'g',

            // Russian
            'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
            'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
            'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'Kh', 'Ц' => 'Ts',
            'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Shch', 'Ъ' => 'U', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
            'Я' => 'Ya',
            'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
            'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
            'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'kh', 'ц' => 'ts',
            'ч' => 'ch', 'ш' => 'sh', 'щ' => 'shch', 'ъ' => 'u', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
            'я' => 'ya',

            // Ukrainian
            'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
            'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',

            // Czech
            'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
            'Ž' => 'Z',
            'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
            'ž' => 'z',

            // Polish
            'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'E', 'Ł' => 'L', 'Ń' => 'N', 'Ś' => 'S', 'Ź' => 'Z',
            'Ż' => 'Z',
            'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ś' => 's', 'ź' => 'z',
            'ż' => 'z',

            // Latvian
            'Ā' => 'A', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'I', 'Ķ' => 'K', 'Ļ' => 'L', 'Ņ' => 'N', 'Ū' => 'U',
            'ā' => 'a', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n', 'ū' => 'u',
        ];

        return strtr($text, $characterMap);
    }

    /**
     * Sanitizes an anchor text by removing special characters, replacing spaces with dashes,
     * and removing consecutive dashes.
     *
     * @param  string $text The anchor text to sanitize.
     * @return string The sanitized anchor text.
     */
    protected function sanitizeAnchor(string $text): string
    {
        $delimiter = $this->options['delimiter'];
        // Replace non-alphanumeric characters with our delimiter
        $text = preg_replace('/[^\p{L}\p{Nd}]+/u', $delimiter, $text);
        // Remove consecutive delimiters
        $text = preg_replace('/(' . preg_quote($delimiter, '/') . '){2,}/', '$1', $text);
        // Remove leading and trailing delimiters
        $text = trim($text, $delimiter);
        return $text;
    }

    /**
     * Generate a unique anchor ID based on the given text.
     *
     * @param  string $text The text to generate the anchor ID from.
     * @return string The unique anchor ID.
     */
    protected function uniquifyAnchorID(string $text): string
    {
        $blacklist = $this->options['blacklist'];

        // Initialize the count for this text if not already set
        if (!isset($this->anchorDuplicates[$text])) {
            $this->anchorDuplicates[$text] = 0;
        }

        // If the text is not in the blacklist and is the first time we see it, return it as is
        if (!in_array($text, $blacklist) && $this->anchorDuplicates[$text] === 0) {
            // Increment here to account for the next time we see this text
            $this->anchorDuplicates[$text]++;
            return $text; // Return without adding a count
        }

        // For subsequent duplicates, start appending a number starting from 1
        $originalText = $text;

        /**
         * @psalm-suppress all
         * Workaround for Psalm as UnsupportedPropertyReferenceUsage can't be suppressed
         */
        $count = &$this->anchorDuplicates[$originalText];

        // Generate a unique anchor ID by appending a count to the original text
        while (true) {
            if ($count > 0) { // Only append the count if it's not the first occurrence
                $text = $originalText . '-' . $count;
                if (!in_array($text, $blacklist) && !isset($this->anchorDuplicates[$text])) {
                    break;
                }
            }
            $count++;
        }

        // Increment the count for the next duplicate
        $this->anchorDuplicates[$text] = 1; // Initialize the duplicate counter for the new unique text
        $count++; // Prepare for the next potential duplicate

        return $text;
    }



    /**
     * Decodes the hashed ToC tag to an original tag and replaces.
     *
     * This is used to avoid parsing user defined ToC tag which includes "_" in
     * their tag such as "[[_]]". Unless it will be parsed as:
     *   "<p>[[<em>TOC</em>]]</p>"
     *
     * @param  string $text
     * @return string
     */
    protected function decodeTagFromHash(string $text): string
    {
        $salt = $this->getSalt();
        $tag_origin = $this->getTocTag();
        $tag_hashed = hash('sha256', $salt . $tag_origin);

        if (strpos($text, $tag_hashed) === false) {
            return $text;
        }

        return str_replace($tag_hashed, $tag_origin, $text);
    }

    /**
     * Encodes the ToC tag to a hashed tag and replace.
     *
     * This is used to avoid parsing user defined ToC tag which includes "_" in
     * their tag such as "[[_]]". Unless it will be parsed as:
     *   "<p>[[<em>TOC</em>]]</p>"
     *
     * @param  string $text
     * @return string
     */
    protected function encodeTagToHash(string $text): string
    {
        $salt = $this->getSalt();
        $tag_origin = $this->getTocTag();

        if (strpos($text, $tag_origin) === false) {
            return $text;
        }

        $tag_hashed = hash('sha256', $salt . $tag_origin);

        return str_replace($tag_origin, $tag_hashed, $text);
    }

    /**
     * Get only the text from a markdown string.
     * It parses to HTML once then trims the tags to get the text.
     *
     * @param  string $text  Markdown text.
     * @return string
     */
    protected function fetchText(string $text): string
    {
        return trim(strip_tags($this->line($text)));
    }

    /**
     * Gets the ID attribute of the ToC for HTML tags.
     *
     * @return string
     */
    protected function getTocIdAttribute(): string
    {
        return $this->options['toc_id'];
    }

    /**
     * Unique string to use as a salt value.
     *
     * @return string
     */
    protected function getSalt(): string
    {
        static $salt;
        if (isset($salt)) {
            return $salt;
        }

        $salt = hash('md5', strval(time()));
        return $salt;
    }

    /**
     * Gets the markdown tag for ToC.
     *
     * @return string
     */
    protected function getTocTag(): string
    {
        return $this->options['toc_tag'];
    }

    /**
     * Set/stores the heading block to ToC list in a string and array format.
     *
     * @param  array $Content   Heading info such as "level","id" and "text".
     * @return void
     */
    protected function setContentsList(array $Content): void
    {
        // Stores as an array
        $this->setContentsListAsArray($Content);
        // Stores as string in markdown list format.
        $this->setContentsListAsString($Content);
    }

    /**
     * Sets/stores the heading block info as an array.
     *
     * @param  array $Content
     * @return void
     */
    protected function setContentsListAsArray(array $Content): void
    {
        $this->contentsListArray[] = $Content;
    }

    /**
     * Sets/stores the heading block info as a list in markdown format.
     *
     * @param  array $Content  Heading info such as "level","id" and "text".
     * @return void
     */
    protected function setContentsListAsString(array $Content): void
    {
        $text = $this->fetchText($Content['text']);
        $id = $Content['id'];
        $level = (int) trim($Content['level'], 'h');
        $link = "[{$text}](#{$id})";

        if ($this->firstHeadLevel === 0) {
            $this->firstHeadLevel = $level;
        }
        $indentLevel = max(1, $level - ($this->firstHeadLevel - 1));
        $indent = str_repeat('  ', $indentLevel);

        $this->contentsListString .= "{$indent}- {$link}" . PHP_EOL;
    }

    /**
     * Parses markdown string to HTML and also the "[toc]" tag as well.
     * It overrides the parent method: \Parsedown::text().
     *
     * @param  string $text
     * @return string
     */
    public function text($text): string
    {
        // Parses the markdown text except the ToC tag. This also searches
        // the list of contents and available to get from "contentsList()"
        // method.
        $html = $this->body($text);

        $tag_origin  = $this->getTocTag();

        if (strpos($text, $tag_origin) === false) {
            return $html;
        }

        $data = $this->contentsList();
        $toc_id   = $this->getTocIdAttribute();
        $needle  = '<p>' . $tag_origin . '</p>';
        $replace = "<div id=\"{$toc_id}\">{$data}</div>";

        return str_replace($needle, $replace, $html);
    }
}
