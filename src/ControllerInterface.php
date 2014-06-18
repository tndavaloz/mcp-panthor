<?php
/**
 * @copyright ©2014 Quicken Loans Inc. All rights reserved. Trade Secret,
 *    Confidential and Proprietary. Any dissemination outside of Quicken Loans
 *    is strictly prohibited.
 */

namespace QL\Panthor;

interface ControllerInterface
{
    /**
     * The primary action of this controller. Any return from this method is ignored.
     *
     * @return null
     */
    public function __invoke();
}
