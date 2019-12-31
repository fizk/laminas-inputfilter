<?php

/**
 * @see       https://github.com/laminas/laminas-inputfilter for the canonical source repository
 * @copyright https://github.com/laminas/laminas-inputfilter/blob/master/COPYRIGHT.md
 * @license   https://github.com/laminas/laminas-inputfilter/blob/master/LICENSE.md New BSD License
 */

namespace LaminasTest\InputFilter;

use Laminas\Filter;
use Laminas\InputFilter\CollectionInputFilter;
use Laminas\InputFilter\Factory;
use Laminas\InputFilter\Input;
use Laminas\InputFilter\InputFilter;
use PHPUnit_Framework_TestCase as TestCase;

class InputFilterTest extends TestCase
{
    public function setUp()
    {
        $this->filter = new InputFilter();
    }

    public function testLazilyComposesAFactoryByDefault()
    {
        $factory = $this->filter->getFactory();
        $this->assertInstanceOf('Laminas\InputFilter\Factory', $factory);
    }

    public function testCanComposeAFactory()
    {
        $factory = new Factory();
        $this->filter->setFactory($factory);
        $this->assertSame($factory, $this->filter->getFactory());
    }

    public function testCanAddUsingSpecification()
    {
        $this->filter->add(array(
            'name' => 'foo',
        ));
        $this->assertTrue($this->filter->has('foo'));
        $foo = $this->filter->get('foo');
        $this->assertInstanceOf('Laminas\InputFilter\InputInterface', $foo);
    }

    /**
     * @covers \Laminas\InputFilter\BaseInputFilter::getValue
     *
     * @group 6028
     */
    public function testGetValueReturnsArrayIfNestedInputFilters()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(new Input(), 'name');

        $this->filter->add($inputFilter, 'people');

        $data = array(
            'people' => array(
                 'name' => 'Wanderson'
            )
        );

        $this->filter->setData($data);
        $this->assertTrue($this->filter->isValid());

        $this->assertInternalType('array', $this->filter->getValue('people'));
    }

    /**
     * @group Laminas-5648
     */
    public function testCountZeroValidateInternalInputWithCollectionInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(new Input(), 'name');

        $collection = new CollectionInputFilter();
        $collection->setInputFilter($inputFilter);
        $collection->setCount(0);

        $this->filter->add($collection, 'people');

        $data = array(
            'people' => array(
                array(
                    'name' => 'Wanderson',
                ),
            ),
        );
        $this->filter->setData($data);

        $this->assertTrue($this->filter->isvalid());
        $this->assertSame($data, $this->filter->getValues());
    }
}
