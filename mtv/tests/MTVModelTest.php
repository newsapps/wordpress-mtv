<?php

require_once 'MTVTest.php';

class MTVModelTest extends MTVTest {

    public function setUp() {
        check_before_wreck();

        $this->model = new \mtv\models\Model(
            array('name' => 'test_model')
        );
    }

    public function test_save() {
        try {
            $this->model->save();
        } catch (NotImplementedException $expected) {
            return;
        }

        $this->fail('An expected NotImplementedException has not been raised.');
    }

    public function test_validate() {
        try {
            $this->model->save();
        } catch (NotImplementedException $expected) {
            return;
        }

        $this->fail('An expected NotImplementedException has not been raised.');
    }

    public function test_initialize() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test___toString() {
        $ret = $this->model->__toString();

        $this->assertEquals($ret, 'mtv\models\Model');
    }

    public function test___get() {
        $ret = $this->model->__get('name');

        $this->assertEquals($ret, 'test_model');
    }

    public function test___set() {
        $this->model->__set('name', 'name_changed_with_set');

        $this->assertEquals($this->model->attributes['name'], 'name_changed_with_set');
    }

    public function test___unset() {
        $this->model->__unset('name');

        $this->assertTrue(empty($this->model->attributes['name']));
    }

    public function test___isset() {
        $ret = $this->model->__isset('name');

        $this->assertTrue($ret);
    }

    public function test_clear() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_set() {
        // Set attribute values, verify
        $attrs = array(
            'name' => 'changed_name',
            'note' => 'Just a test'
        );
        $this->model->set($attrs);

        $this->assertEquals($this->model->attributes['name'], 'changed_name');
        $this->assertEquals($this->model->attributes['note'], 'Just a test');
    }

    public function test_fetch() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_parse() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_reload() {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    public function test_to_json() {
        // Return assoc array of jsonable fields for passing to json_encode()
        $this->model->json_fields = array('name');
        $ret = $this->model->to_json();

        $this->assertEquals($ret, array('name' => 'test_model'));
    }

    public function test_set_from_json() {
        // Provide data for the model as a json string,
        // check that the attributes are set properly
        $json = '{"name": "changed_name_with_json"}';
        $this->model->set_from_json($json);

        $this->assertEquals(
            $this->model->attributes['name'], 'changed_name_with_json');
    }

}
