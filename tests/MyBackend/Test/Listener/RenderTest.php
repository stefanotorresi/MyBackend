<?php
/**
 * @author Stefano Torresi (http://stefanotorresi.it)
 * @license See the file LICENSE.txt for copying permission.
 * ************************************************
 */

namespace MyBackend\Test\Listener;

use MyBackend\Listener\Render;
use PHPUnit_Framework_TestCase;
use Zend\EventManager\EventManager;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\CallbackHandler;

class RenderTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var EventManager
     */
    protected $eventManager;

    /**
     * @var Render
     */
    protected $listener;

    public function setUp()
    {
        $this->eventManager = new EventManager();
        $this->listener = new Render();
    }

    public function testAttach()
    {
        $this->listener->attach($this->eventManager);

        $handlers = $this->eventManager->getListeners(MvcEvent::EVENT_RENDER)->toArray();

        $this->assertCount(1, $handlers);
        $callbackHandler = $handlers[0]; /** @var CallbackHandler $callbackHandler */
        $callbackArray = $callbackHandler->getCallback();
        $this->assertSame($this->listener, $callbackArray[0]);
        $this->assertEquals('prepareLayout', $callbackArray[1]);
        $this->assertEquals($callbackHandler->getMetadatum('priority'), -1);

        $handlers = $this->eventManager->getListeners(MvcEvent::EVENT_RENDER_ERROR)->toArray();

        $this->assertCount(1, $handlers);
        $callbackHandler = $handlers[0]; /** @var CallbackHandler $callbackHandler */
        $callbackArray = $handlers[0]->getCallback();
        $this->assertSame($this->listener, $callbackArray[0]);
        $this->assertEquals('prepareLayout', $callbackArray[1]);
        $this->assertEquals($callbackHandler->getMetadatum('priority'), -101);
    }
}
