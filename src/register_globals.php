<?php
namespace { // global namespace

	use RadSectors\SqlShim\SqlShim;

	if ( !function_exists('sqlsrv_connect') ) {

		/**
		 * CONSTANTS
		 */
		define('SQLSRV_FETCH_NUMERIC', 1);
		define('SQLSRV_FETCH_ASSOC', 2);
		define('SQLSRV_FETCH_BOTH', 3);

		define('SQLSRV_SCROLL_NEXT', 1);
		define('SQLSRV_SCROLL_FIRST', 2);
		define('SQLSRV_SCROLL_LAST', 3);
		define('SQLSRV_SCROLL_PRIOR', 4);
		define('SQLSRV_SCROLL_ABSOLUTE', 5);
		define('SQLSRV_SCROLL_RELATIVE', 6);

		define('SQLSRV_ERR_ERRORS', 0);
		define('SQLSRV_ERR_WARNINGS', 1);
		define('SQLSRV_ERR_ALL', 2);


		/*function sqlsrv_begin_transaction( $conn )
		{
			return SqlShim::begin_transaction($conn);
		}

		function sqlsrv_cancel( $stmt )
		{
			return SqlShim::cancel($stmt);
		}

		function sqlsrv_client_info( $conn )
		{
			return SqlShim::client_info($conn);
		}*/

		function sqlsrv_close( $stmt )
		{
			return SqlShim::close($stmt);
		}

		/*function sqlsrv_commit( $conn )
		{
			return SqlShim::commit($conn);
		}

		function sqlsrv_configure( $setting, $value )
		{
			return SqlShim::configure($setting, $value);
		}*/

		function sqlsrv_connect( $dbhost, $cnfo )
		{
			return SqlShim::connect($dbhost, $cnfo);
		}

		function sqlsrv_errors( $errorsOrWarnings=SQLSRV_ERR_ALL )
		{
			return SqlShim::errors($errorsOrWarnings);
		}

		/*function sqlsrv_execute( $stmt )
		{
			return SqlShim::execute($stmt);
		}*/

		function sqlsrv_fetch_array( $stmt, $fetchType=SQLSRV_FETCH_BOTH,
			$row=SQLSRV_SCROLL_NEXT, $offset=0 )
		{
			return SqlShim::fetch_array($stmt, $fetchType, $row, $offset);
		}

		function sqlsrv_fetch_object( $stmt, $className='stdClass',
			$ctorParams=array(), $row=SQLSRV_SCROLL_NEXT, $offset=0 )
		{
			return SqlShim::fetch_object($stmt, $className, $ctorParams, $row, $offset);
		}

		/*function sqlsrv_fetch( $stmt, $row=SQLSRV_SCROLL_NEXT, $offset=0 )
		{
			return SqlShim::fetch($stmt, $row, $offset);
		}

		function sqlsrv_field_metadata( $stmt )
		{
			return SqlShim::field_metadata($stmt);
		}*/

		function sqlsrv_free_stmt( $stmt )
		{
			return SqlShim::free_stmt($stmt);
		}

		/*function sqlsrv_get_config( $setting )
		{
			return SqlShim::get_config($setting);
		}

		function sqlsrv_get_field( $stmt, $fieldIndex=0, $getAsType )
		{
			return SqlShim::get_field($stmt, $fieldIndex=0, $getAsType);
		}

		function sqlsrv_has_rows( $stmt )
		{
			return SqlShim::has_rows($stmt);
		}

		function sqlsrv_next_result( $stmt )
		{
			return SqlShim::next_result($stmt);
		}

		function sqlsrv_num_fields( $stmt )
		{
			return SqlShim::num_fields($stmt);
		}

		function sqlsrv_num_rows( $stmt )
		{
			return SqlShim::num_rows($stmt);
		}

		function sqlsrv_prepare( $conn, $sql, $params=[], $options=[] )
		{
			return SqlShim::prepare($conn, $sql, $params, $options);
		}*/

		function sqlsrv_query( $conn, $sql, $params=[], $options=[] )
		{
			return SqlShim::query($conn, $sql, $params, $options);
		}

		function sqlsrv_rollback( $conn )
		{
			return SqlShim::rollback($conn);
		}

		function sqlsrv_rows_affected( $stmt )
		{
			return SqlShim::rows_affected($stmt);
		}

		/*function sqlsrv_send_stream_data( $stmt )
		{
			return SqlShim::send_stream_data($stmt);
		}

		function sqlsrv_server_info( $conn )
		{
			return SqlShim::server_info($conn);
		}*/

	}

}