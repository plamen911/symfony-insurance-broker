<?php
declare(strict_types=1);

namespace Tests\AppBundle\Service;

use AppBundle\Service\CommonService;
// use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use PHPUnit\Framework\TestCase;

/**
 * Class CommonServiceTest
 * @package AppBundle\Tests\Service
 * @author Plamen Markov <plamen@lynxlake.org>
 */
class CommonServiceTest extends TestCase
{
    public function testGenerateCustomRange_add_correctInput_expectRangeArray()
    {
        $commonService = new CommonService();
        $result = $commonService->generateCustomRange('TEST-0001', 'TEST-0004');

        // assert that result is array
        $this->assertTrue(is_array($result));
        // assert the length of array
        $this->assertEquals(4, count($result));
        // assert the first element of array
        $this->assertEquals('TEST-0001', $result[0]);
        // assert the second element of array
        $this->assertEquals('TEST-0002', $result[1]);
        // assert the last element of array
        $this->assertEquals('TEST-0004', $result[count($result) - 1]);
    }
}
