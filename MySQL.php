<?

	/**
	 * Class for managing a single MySQL database connection, including convenience 
	 * methods for handling parameterized queries.
	 * 
	 * @author dlane
	 */
	class MySQL{
		
		private $config = null;
		private $connection = null;
		
		function __construct(){
			
			// TODO replace this array with your DB connection settings.
			// you should do something better, like include settings from an INI file; this is just an example.
			$config = array(
				'hostname' => 'localhost',
				'username' => 'user',
				'password' => 'pass',
				'database' => 'my_project_db'
			);
			
			$this->connect();
		}
		
		function __destruct(){
			
			$this->close();
		}
		
		private function close(){
			
			if( $this->connection != null ){
				
				mysqli_close( $this->connection );
				$this->connection = null;
			}
		}
		
		private function connect(){
			
			if( $this->connection == null ){
				
				$config = $this->config;
				$this->connection = mysqli_connect( $config['hostname'], $config['username'], $config['password'], $config['database'] );
			}
		}
		
		function getConnection(){
			
			if( $this->connection == null ){
				
				$this->connect();
			}
			
			return $this->connection;
		}
		
		/**
		 * Retrieves a complete result set for a parameterized query with one or more parameters.
		 * 
		 * @param $query  a parameterized query.
		 * @param $types  types of values to be bound to the parameterized query.
		 * @param $bindParams  array of values to be bound to the parameterized query.
		 * @param $fixSingleItemArray  if true, a one-record result set will be returned as a record instead of an array containing a record.
		 * 
		 * @return mixed  result set similar to an array of rows from mysql_fetch_array().
		 */
		function parameterizedQuery( $query, $types, $bindParams=array(), $fixSingleItemArray=true ){
			
			$statement = mysqli_stmt_init( $this->getConnection() );
			mysqli_stmt_prepare( $statement, $query );
			call_user_func_array( 'mysqli_stmt_bind_param', array_merge( array( $statement, $types ), $bindParams ) );
			mysqli_stmt_execute( $statement );
			
			$result = array();
			while( $row = $this->parameterizedFetchArray( $statement ) ){
				
				if( $row['hasMoreResults'] != true ){
					
					break;
				}
				
				$result[] = $row;
			}
			
			mysqli_stmt_close( $statement );
			
			$numResults = sizeof( $result );
			if( $fixSingleItemArray && $numResults == 1 ){
				
				return $result[0];
			}
			else if( $numResults == 0 ){
				
				return null;
			}
			
			return $result;
		}
		
		/**
		 * Retrieves a record set for the given mysqli statement (parameterized query).
		 * 
		 * @param $statement  an initialized mysqli statement.
		 * 
		 * @return array  single record set (similar to calling mysql_fetch_array()).
		 */
		private function parameterizedFetchArray( $statement ) {
			
	        $metadata = mysqli_stmt_result_metadata( $statement );
	        $count = 1; //start the count from 1. First value has to be a reference to the stmt. because bind_param requires the link to $stmt as the first param.
	        $fieldNames[0] = &$statement;
	        while ( $field = mysqli_fetch_field( $metadata ) ) {
	            
	            $fieldNames[$count] = &$row[$field->name]; //load the fieldnames into an array.
	            $count++;
	        }
	        
	        call_user_func_array( 'mysqli_stmt_bind_result', $fieldNames );
	        $row['hasMoreResults'] = mysqli_stmt_fetch( $statement );
	        return $row;
	    }
	}

?>
