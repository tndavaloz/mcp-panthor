<?php
/**
 * @copyright ©2014 Quicken Loans Inc. All rights reserved. Trade Secret,
 *    Confidential and Proprietary. Any dissemination outside of Quicken Loans
 *    is strictly prohibited.
 */

namespace QL\Panthor\Testing;

use Mockery;

trait MockeryAssistantTrait
{
    private $spies;

    /**
     * Spy on an expectation and record the output for further tests and assertions.
     *
     * Any parameter that is a spy will be replaced with a closure that captures output into the spy.
     *
     * Usage:
     * $mock = Mockery::mock('ClassName');
     * $spy = new Spy;
     * $spy2 = new Spy;
     *
     * // Use the spy at the parameter position you wish to capture.
     * // You can use multiple spies within the same call.
     * $this->spy($mock, 'methodName', [$list, $of, $parameters, $spy, $spy2]);
     *
     * // Run code
     *
     * // Invoke the spy to return its captured output
     * $captured = $spy1();
     *
     * @param $mock
     * @param string $method Method to spy on
     * @param array $parameters
     *
     * @return $mock
     */
    public function spy($mock, $method, array $parameters)
    {
        $should = $mock->shouldReceive($method);

        array_walk($parameters, function(&$v) {
            if ($v instanceof Spy) {
                $spy = $v;

                $v = Mockery::on(function($v) use ($spy) {
                    $spy($v);
                    return true;
                });
            }
        });

        $with = call_user_func_array([$should, 'with'], $parameters);

        return $with;
    }

    /**
     * Build a spy and store it on the class
     *
     * @return Spy
     */
    public function buildSpy($name)
    {
        if (!is_array($this->spies)) {
            $this->spies = [];
        }

        $spy = new Spy;
        $this->spies[$name] = $spy;

        return $spy;
    }

    /**
     * Get a spy previously built.
     *
     * @return Spy|null
     */
    public function getSpy($name)
    {
        if (isset($this->spies[$name])) {
            return $this->spies[$name];
        }
    }
}
