<?php

namespace Webmozart\PhpScoper\Util;

class MutableString
{
    /**
     * @var string
     */
    private $string;

    /**
     * @var array[pos, len, newString]
     */
    private $modifications = [];

    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * @param $pos
     * @param $newString
     */
    public function insert($pos, $newString)
    {
        $this->modifications[] = [$pos, 0, $newString];
    }

    /**
     * @param $pos
     * @param $len
     */
    public function remove($pos, $len)
    {
        $this->modifications[] = [$pos, $len, ''];
    }

    /**
     * @param $str
     * @param $startPos
     *
     * @return bool|int
     */
    public function indexOf($str, $startPos)
    {
        return strpos($this->string, $str, $startPos);
    }

    /**
     * @return string
     */
    public function getOrigString()
    {
        return $this->string;
    }

    /**
     * @return string
     */
    public function getModifiedString()
    {
        // Sort by position
        usort($this->modifications, function ($a, $b) {
            if ($a[0] === $b[0]) {
                return 0;
            }

            return $a[0] > $b[0] ? 1 : -1;
        });
        $result = '';
        $startPos = 0;

        foreach ($this->modifications as list($pos, $len, $newString)) {
            $result .= substr($this->string, $startPos, $pos - $startPos);
            $result .= $newString;
            $startPos = $pos + $len;
        }

        $result .= substr($this->string, $startPos);

        return $result;
    }
}
