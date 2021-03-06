<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   Copyright (c) 2013 X.commerce, Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_Core_Model_Resource_Layout_Link_CollectionTest extends Mage_Core_Model_Resource_Layout_AbstractTestCase
{
    /**
     * Name of test table
     */
    const TEST_TABLE = 'core_layout_update';

    /**
     * Name of main table alias
     *
     * @var string
     */
    protected $_tableAlias = 'update';

    /**
     * @param Zend_Db_Select $select
     * @return Mage_Core_Model_Resource_Layout_Link_Collection
     */
    protected function _getCollection(Zend_Db_Select $select)
    {
        return new Mage_Core_Model_Resource_Layout_Link_Collection($this->_getResource($select));
    }

    /**
     * @dataProvider filterFlagDataProvider
     * @param bool $flag
     */
    public function testAddTemporaryFilter($flag)
    {
        $select = $this->getMock('Zend_Db_Select', array(), array('where'), '', false);
        $select->expects($this->once())
            ->method('where')
            ->with(self::TEST_WHERE_CONDITION);

        $collection = $this->_getCollection($select);

        /** @var $connection PHPUnit_Framework_MockObject_MockObject */
        $connection = $collection->getResource()->getReadConnection();
        $connection->expects($this->any())
            ->method('prepareSqlCondition')
            ->with('main_table.is_temporary', $flag)
            ->will($this->returnValue(self::TEST_WHERE_CONDITION));

        $collection->addTemporaryFilter($flag);
    }

    /**
     * @return array
     */
    public function filterFlagDataProvider()
    {
        return array(
            'Add temporary filter'     => array('$flag' => true),
            'Disable temporary filter' => array('$flag' => false),
        );
    }

    /**
     * @covers Mage_Core_Model_Resource_Layout_Link_Collection::_joinWithUpdate
     */
    public function testJoinWithUpdate()
    {
        $select = $this->getMock('Zend_Db_Select', array(), array(), '', false);
        $select->expects($this->once())
            ->method('join')
            ->with(
                array('update' => self::TEST_TABLE),
                'update.layout_update_id = main_table.layout_update_id',
                $this->isType('array')
            );

        $collection = $this->_getCollection($select);

        /** @var $resource PHPUnit_Framework_MockObject_MockObject */
        $resource = $collection->getResource();
        $resource->expects($this->once())
            ->method('getTable')
            ->with(self::TEST_TABLE)
            ->will($this->returnValue(self::TEST_TABLE));

        $collection->addUpdatedDaysBeforeFilter(1)
            ->addUpdatedDaysBeforeFilter(2);
    }
}
