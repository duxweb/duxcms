<?php

namespace App\Spider\Pkg;

class HtmlContentExtractors
{
    public string $content_start_pos = '';
    public string $content_end_pos = '';
    public string $content_center_pos = '';
    public array $paragraphs = [];

    public function get_contents($html) {
        $paragraphs = $this->del_html_tag($html, true);
        $paragraphs = explode('\n', $paragraphs);

        $paragraph_lengths = array_map('strlen', array_map('strip_tags', $paragraphs));
        $paragraph_block_lengths = array_map(function ($x) use ($paragraph_lengths) {
            return array_sum(array_slice($paragraph_lengths, $x, Settings::MAX_PARAGRAPH_DISTANCE));
        }, range(0, count($paragraph_lengths) - Settings::MAX_PARAGRAPH_DISTANCE));

        $this->content_center_pos = $content_start_pos = $content_end_pos = array_search(max($paragraph_block_lengths), $paragraph_block_lengths);
        $min_paragraph_block_length = Settings::MIN_PARAGRAPH_LENGHT * Settings::MAX_PARAGRAPH_DISTANCE;

        while ($content_start_pos > 0 && $paragraph_block_lengths[$content_start_pos] > $min_paragraph_block_length) {
            $content_start_pos--;
        }

        while ($content_end_pos < count($paragraph_block_lengths) && $paragraph_block_lengths[$content_end_pos] > $min_paragraph_block_length) {
            $content_end_pos++;
        }

        $content = implode('\n', array_slice($paragraphs, $content_start_pos, $content_end_pos - $content_start_pos));
        $content = $this->del_unnecessary_character($content);

        $paragraphs_text_len = strlen(strip_tags($this->get_info($content, '/<p.*?>(.*?)<\/p>/')));
        $content_text_len = strlen(strip_tags($content));

        if ($content_text_len > 0 && $content_text_len > Settings::MIN_COUNTENT_WORDS && ($paragraphs_text_len / $content_text_len) > Settings::MIN_PARAGRAPH_AND_CONTENT_PROPORTION) {
            $this->content_start_pos = $content_start_pos;
            $this->content_end_pos = $content_end_pos;
            $this->paragraphs = $paragraphs;
            return $content;
        } else {
            return '';
        }
    }

    private function del_html_tag($html, $save_useful_tag = false) {
        $html = $this->replace_str($html, '/(?i)<script(.|\n)*?<\/script>/', '');
        $html = $this->replace_str($html, '/(?i)<style(.|\n)*?<\/style>/', '');
        $html = $this->replace_str($html, '/<!--(.|\n)*?-->/', '');
        $html = $this->replace_str($html, '/(?!&[a-z]+=)&[a-z]+;?/', ' ');
        $html = $this->replace_str($html, '/<input\s*(.*?)\s*\/>/', '');

        if ($save_useful_tag) {
            $useful_tag_regex = implode('|', USEFUL_TAG);
            $html = $this->replace_str($html, "/(?!$useful_tag_regex)<(.|\n)+?>/", '');
        } else {
            $html = $this->replace_str($html, '/<(.|\n)*?>/', '');
        }

        $html = $this->replace_str($html, '/[\f\r\t\v]/', '');
        return trim($html);
    }

    private function replace_str($source_str, $regex, $replace_str = '') {
        return preg_replace($regex, $replace_str, $source_str);
    }

    private function del_unnecessary_character($content) {
        $content = trim($content);
        $content = strpos($content, '>') === 0 ? substr($content, strpos($content, '>') + 1) : $content;
        $content = $this->replace_str($content, '/ {2,}/', '');
        return $this->replace_str($content, '/(?! )\s+/', '\n');
    }

    private function get_info($html, $regexs, $allow_repeat = false, $fetch_one = false, $split = null) {
        $regexs = is_string($regexs) ? [$regexs] : $regexs;
        $infos = [];

        foreach ($regexs as $regex) {
            if ($regex === '') {
                continue;
            }

            if (!isset($this->_regexs[$regex])) {
                $this->_regexs[$regex] = $regex;
            }

            if ($fetch_one) {
                preg_match($this->_regexs[$regex], $html, $match);
                $infos = $match ? $match : [];
            } else {
                preg_match_all($this->_regexs[$regex], $html, $matches);
                $infos = $matches[0];
            }

            if (count($infos) > 0) {
                break;
            }
        }

        if ($fetch_one) {
            $infos = count($infos) > 1 ? $infos : ($infos[0] ?? '');
            return is_array($infos) ? (count($infos) > 1 ? $infos : ($infos[0] ?? '')) : $infos;
        } else {
            $infos = $allow_repeat ? $infos : array_unique($infos);
            return $split ? implode($split, $infos) : $infos;
        }
    }
}