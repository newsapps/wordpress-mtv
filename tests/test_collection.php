<?php

require_once 'lib/mtv_test_classes.php';

class MTVCollectionTest extends MTVTest {

    public function setUp() {
        // Initialize a bare Collection
        $this->collection = new \mtv\models\Collection;
    }

    public function test_current() {
        // Bare Collection is initialized with no models
        $ret = $this->collection->current();

        $this->assertEquals($ret, false);
    }

    public function test_next() {
        // Shouldn't be any models to iterate over
        $ret = $this->collection->next();
        $this->assertEquals($ret, false);

        // Add two models and test
        $this->collection->add(
            new \mtv\models\Model(array('name' => 'one')));
        $this->collection->add(
            new \mtv\models\Model(array('name' => 'two')));

        $ret = $this->collection->next(); // Forward one
        $this->assertEquals($ret->attributes['name'], 'two');
    }

    public function test_key() {
        $ret = $this->collection->key();

        $this->assertEquals($ret, null);
    }

    public function test_valid() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_rewind() {
        // Shouldn't be any models to iterate over
        $ret = $this->collection->rewind();
        $this->assertEquals($ret, false);

        // Add two models and test
        $this->collection->add(
            new \mtv\models\Model(array('name' => 'one')));
        $this->collection->add(
            new \mtv\models\Model(array('name' => 'two')));

        $ret = $this->collection->next(); // Forward one
        $ret = $this->collection->rewind(); // Back one

        $this->assertEquals($ret->attributes['name'], 'one');
    }

    public function test_offsetExists() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_offsetGet() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_offsetSet() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_offsetUnset() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_count() {
        $ret = $this->collection->count();

        $this->assertEquals($ret, 0);
    }

    public function test_add() {
        $this->collection->add(new \mtv\models\Model);

        $this->assertEquals(count($this->collection->models), 1);
        $this->assertTrue(
            is_a($this->collection->models[0], 'mtv\models\Model'));
    }

    public function test_clear() {
        // Add a model
        $this->collection->add(new \mtv\models\Model);
        // Clear all
        $this->collection->clear();

        // Expect an empty array
        $this->assertEquals($this->collection->models, array());
    }

    public function test_to_json() {
        // Add empty Model
        $this->collection->add(new \mtv\models\Model);

        $ret = $this->collection->to_json();

        // to_json should return an array with one empty array
        $this->assertEquals($ret, array(array()));
    }

    public function test_get() {
        /*
         * Collection::get() calls its static model's fetch()
         * method. In this case, the base Model's fetch
         * method is not implemented and throws an exception.
         * TODO: Figure a way to avoid the exception and test
         * the expected return value of Collection::get()
         */
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_get_by() {
        try {
            $this->collection->get_by(array());
        } catch (NotImplementedException $expected) {
            return;
        }

        $this->fail('An expected NotImplementedException has not been raised.');
    }

    public function test_filter() {
        try {
            $this->collection->filter(array());
        } catch (NotImplementedException $expected) {
            return;
        }

        $this->fail('An expected NotImplementedException has not been raised.');
    }

}
