<?php

use WP_CLI\Tests\TestCase;

class AddRemoveTest extends TestCase {

	protected static $test_config_path;
	protected static $config_transformer;
	protected static $raw_data    = array();
	protected static $string_data = array();

	public static function set_up_before_class() {
		self::$raw_data    = explode( PHP_EOL, file_get_contents( __DIR__ . '/fixtures/raw-data.txt' ) );
		self::$string_data = explode( PHP_EOL, file_get_contents( __DIR__ . '/fixtures/string-data.txt' ) );

		if ( version_compare( PHP_VERSION, '7.0', '>=' ) ) {
			self::$raw_data = array_merge( self::$raw_data, explode( PHP_EOL, file_get_contents( __DIR__ . '/fixtures/raw-data-extra.txt' ) ) );
		}

		self::$test_config_path = __DIR__ . '/wp-config-test-add.php';
		copy( __DIR__ . '/fixtures/wp-config-example.php', self::$test_config_path );
		self::$config_transformer = new WPConfigTransformer( self::$test_config_path );
	}

	public static function tear_down_after_class() {
		unlink( self::$test_config_path );
	}

	public function testAddRawConstants() {
		foreach ( self::$raw_data as $d => $data ) {
			$name = "TEST_CONST_ADD_RAW_{$d}";
			$this->assertTrue( self::$config_transformer->add( 'constant', $name, $data, array( 'raw' => true ) ), $name );
			$this->assertTrue( self::$config_transformer->exists( 'constant', $name ), $name );
		}
	}

	public function testAddStringConstants() {
		foreach ( self::$string_data as $d => $data ) {
			$name = "TEST_CONST_ADD_STRING_{$d}";
			$this->assertTrue( self::$config_transformer->add( 'constant', $name, $data ), $name );
			$this->assertTrue( self::$config_transformer->exists( 'constant', $name ), $name );
		}
	}

	public function testAddRawVariables() {
		foreach ( self::$raw_data as $d => $data ) {
			$name = "test_var_add_raw_{$d}";
			$this->assertTrue( self::$config_transformer->add( 'variable', $name, $data, array( 'raw' => true ) ), "\${$name}" );
			$this->assertTrue( self::$config_transformer->exists( 'variable', $name ), "\${$name}" );
		}
	}

	public function testAddStringVariables() {
		foreach ( self::$string_data as $d => $data ) {
			$name = "test_var_add_string_{$d}";
			$this->assertTrue( self::$config_transformer->add( 'variable', $name, $data ), "\${$name}" );
			$this->assertTrue( self::$config_transformer->exists( 'variable', $name ), "\${$name}" );
		}
	}

	public function testConstantNoAddIfExists() {
		$name = 'TEST_CONST_ADD_EXISTS';
		$this->assertTrue( self::$config_transformer->add( 'constant', $name, 'foo' ), $name );
		$this->assertTrue( self::$config_transformer->exists( 'constant', $name ), $name );
		$this->assertFalse( self::$config_transformer->add( 'constant', $name, 'bar' ), $name );
	}

	public function testVariableNoAddIfExists() {
		$name = 'test_var_add_exists';
		$this->assertTrue( self::$config_transformer->add( 'variable', $name, 'foo' ), "\${$name}" );
		$this->assertTrue( self::$config_transformer->exists( 'variable', $name ), "\${$name}" );
		$this->assertFalse( self::$config_transformer->add( 'variable', $name, 'bar' ), "\${$name}" );
	}

	public function testConfigValues() {
		require_once self::$test_config_path;

		foreach ( self::$raw_data as $d => $data ) {
			// Convert string to a real value.
			eval( "\$data = $data;" ); // phpcs:ignore Squiz.PHP.Eval.Discouraged
			// Raw Constants
			$name = "TEST_CONST_ADD_RAW_{$d}";
			$this->assertTrue( defined( $name ), $name );
			$this->assertEquals( $data, constant( $name ), $name );
			// Raw Variables
			$name = "test_var_add_raw_{$d}";
			$this->assertTrue( ( isset( ${$name} ) || is_null( ${$name} ) ), "\${$name}" );
			$this->assertEquals( $data, ${$name}, "\${$name}" );
		}

		foreach ( self::$string_data as $d => $data ) {
			// String Constants
			$name = "TEST_CONST_ADD_STRING_{$d}";
			$this->assertTrue( defined( $name ), $name );
			$this->assertEquals( $data, constant( $name ), $name );
			// String Variables
			$name = "test_var_add_string_{$d}";
			$this->assertTrue( ( isset( ${$name} ) || is_null( ${$name} ) ), "\${$name}" );
			$this->assertEquals( $data, ${$name}, "\${$name}" );
		}

		$this->assertTrue( defined( 'TEST_CONST_ADD_EXISTS' ), 'TEST_CONST_ADD_EXISTS' );
		$this->assertEquals( 'foo', constant( 'TEST_CONST_ADD_EXISTS' ), 'TEST_CONST_ADD_EXISTS' );

		$this->assertTrue( ( isset( $test_var_add_exists ) || is_null( $test_var_add_exists ) ), '$test_var_update_add_missing' );
		$this->assertEquals( 'foo', $test_var_add_exists, '$test_var_add_exists' );
	}

	public function testRemoveRawConstants() {
		foreach ( self::$raw_data as $d => $data ) {
			$name = "TEST_CONST_ADD_RAW_{$d}";
			$this->assertTrue( self::$config_transformer->exists( 'constant', $name ), $name );
			$this->assertTrue( self::$config_transformer->remove( 'constant', $name ), $name );
			$this->assertFalse( self::$config_transformer->exists( 'constant', $name ), $name );
		}
	}

	public function testRemoveStringConstants() {
		foreach ( self::$string_data as $d => $data ) {
			$name = "TEST_CONST_ADD_STRING_{$d}";
			$this->assertTrue( self::$config_transformer->exists( 'constant', $name ), $name );
			$this->assertTrue( self::$config_transformer->remove( 'constant', $name ), $name );
			$this->assertFalse( self::$config_transformer->exists( 'constant', $name ), $name );
		}
	}

	public function testRemoveRawVariables() {
		foreach ( self::$raw_data as $d => $data ) {
			$name = "test_var_add_raw_{$d}";
			$this->assertTrue( self::$config_transformer->exists( 'variable', $name ), "\${$name}" );
			$this->assertTrue( self::$config_transformer->remove( 'variable', $name ), "\${$name}" );
			$this->assertFalse( self::$config_transformer->exists( 'variable', $name ), "\${$name}" );
		}
	}

	public function testRemoveStringVariables() {
		foreach ( self::$string_data as $d => $data ) {
			$name = "test_var_add_string_{$d}";
			$this->assertTrue( self::$config_transformer->exists( 'variable', $name ), "\${$name}" );
			$this->assertTrue( self::$config_transformer->remove( 'variable', $name ), "\${$name}" );
			$this->assertFalse( self::$config_transformer->exists( 'variable', $name ), "\${$name}" );
		}
	}

	public function testAddConstantNoPlacementAnchor() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Unable to locate placement anchor.' );
		self::$config_transformer->add( 'constant', 'TEST_CONST_ADD_NO_ANCHOR', 'foo', array( 'anchor' => 'nothingtoseehere' ) );
	}

	public function testAddVariableNoPlacementAnchor() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Unable to locate placement anchor.' );
		self::$config_transformer->add( 'variable', 'test_var_add_no_anchor', 'foo', array( 'anchor' => 'nothingtoseehere' ) );
	}

	public function testAddConstantNonString() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Config value must be a string.' );
		self::$config_transformer->add( 'constant', 'TEST_CONST_ADD_NON_STRING', true );
	}

	public function testAddVariableNonString() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Config value must be a string.' );
		self::$config_transformer->add( 'variable', 'test_var_add_non_string', true );
	}

	public function testAddConstantEmptyStringRaw() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Raw value for empty string not supported.' );
		self::$config_transformer->add( 'constant', 'TEST_CONST_ADD_EMPTY_STRING_RAW', '', array( 'raw' => true ) );
	}

	public function testAddVariableEmptyStringRaw() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Raw value for empty string not supported.' );
		self::$config_transformer->add( 'variable', 'test_var_add_empty_string_raw', '', array( 'raw' => true ) );
	}

	public function testAddConstantWhitespaceStringRaw() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Raw value for empty string not supported.' );
		self::$config_transformer->add( 'constant', 'TEST_CONST_ADD_WHITESPACE_STRING_RAW', '   ', array( 'raw' => true ) );
	}

	public function testAddVariableWhitespaceStringRaw() {
		$this->expectException( Exception::class );
		$this->expectExceptionMessage( 'Raw value for empty string not supported.' );
		self::$config_transformer->add( 'variable', 'test_var_add_whitespace_string_raw', '   ', array( 'raw' => true ) );
	}
}
