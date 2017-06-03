<?php
/**
 * UtilitiesTest class file
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

namespace Gestime\CoreBundle\Tests\Business;

use Gestime\CoreBundle\Business\Utilities;
use Gestime\CoreBundle\Tests\KernelAwareTest;

/**
 * UtilitiesTest
 *
 * @category Classes
 * @author   Jean-Loup Empis <jlempis@gmail.com>
 * @version  Release: 2.0.0
 */

class UtilitiesTest extends KernelAwareTest
{
    /**
     * civilité
     * @return string strCivilite
     */
    public function testCivilite()
    {
        parent::setUp();

        $utilities = new Utilities($this->container);

        $result = $utilities->civilite(1);
        $this->assertEquals('Mr', $result);

        $result = $utilities->civilite(2);
        $this->assertEquals('Mme', $result);

        $result = $utilities->civilite(3);
        $this->assertEquals('Melle', $result);

        $result = $utilities->civilite(4);
        $this->assertEquals('Dr', $result);
    }
}
