<?php
declare(strict_types=1);

namespace AppBundle\Service;

/**
 * Class CommonService
 * @package AppBundle\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class CommonService
{
    /**
     * @param string $startIdNumber
     * @param string $endIdNumber
     * @return array
     */
    public function generateCustomRange(string $startIdNumber = '', string $endIdNumber = '')
    {
        $ranges = [];
        if (empty($startIdNumber) || empty($endIdNumber)) {
            return $ranges;
        }

        if (strlen($startIdNumber) < strlen($endIdNumber)) {
            $len = strlen($endIdNumber);
            $startIdNumber = sprintf("%0{$len}s", $startIdNumber);
        }

        $commonPart = '';
        // 1). Detect common part
        for ($i = 0; $i < strlen(strval($startIdNumber)); $i++) {
            $commonPart .= $startIdNumber[$i];
            if (!preg_match('/^' . str_replace('/', '\\/', $commonPart) . '/', $endIdNumber)) {
                $commonPart = substr($commonPart, 0, -1);
                break;
            }
        }

        if (empty($commonPart) && (!is_numeric($startIdNumber) || !is_numeric($endIdNumber))) {
            return $ranges;
        }

        // 2). Extract second part of the number
        $second_part1 = substr($startIdNumber, strlen($commonPart));
        $second_part2 = substr($endIdNumber, strlen($commonPart));

        // 3). Do increment
        $len = strlen($second_part2);
        $intSecond1 = intval($second_part1);
        $intSecond2 = intval($second_part2);

        if ($intSecond1 < $intSecond2) {
            for ($i = $intSecond1; $i <= $intSecond2; $i++) {
                $ranges[] = $commonPart . sprintf("%0{$len}s", $i);
            }

            return $ranges;
        }

        return $ranges;
    }
}
