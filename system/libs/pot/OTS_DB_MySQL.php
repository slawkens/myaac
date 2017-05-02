<?php

/**#@+
 * @version 0.0.1
 */

/**
 * @package POT
 * @version 0.1.3
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * MySQL connection interface.
 * 
 * <p>
 * At all everything that you really need to read from this class documentation is list of parameters for driver's constructor.
 * </p>
 * 
 * @package POT
 * @version 0.1.3
 */
class OTS_DB_MySQL extends OTS_Base_DB
{
/**
 * Creates database connection.
 * 
 * <p>
 * Connects to MySQL database on given arguments.
 * </p>
 * 
 * <p>
 * List of parameters for this drivers:
 * </p>
 * 
 * <ul>
 * <li><var>host</var> - database server.</li>
 * <li><var>port</var> - port (optional, also it is possible to use host:port in <var>host</var> parameter).</li>
 * <li><var>database</var> - database name.</li>
 * <li><var>user</var> - user login.</li>
 * <li><var>password</var> - user password.</li>
 * </ul>
 * 
 * @version 0.0.6
 * @param array $params Connection parameters.
 * @throws PDOException On PDO operation error.
 */
    public function __construct($params)
    {
        $user = null;
        $password = null;
        $dns = array();

        // host:port support
        if( strpos(':', $params['host']) !== false)
        {
            $host = explode(':', $params['host'], 2);

            $params['host'] = $host[0];
            $params['port'] = $host[1];
        }

        if( isset($params['host']) )
        {
            $dns[] = 'host=' . $params['host'];
        }

        if( isset($params['port']) )
        {
            $dns[] = 'port=' . $params['port'];
        }

        if( isset($params['database']) )
        {
            $dns[] = 'dbname=' . $params['database'];
        }

        if( isset($params['user']) )
        {
            $user = $params['user'];
        }

        if( isset($params['password']) )
        {
            $password = $params['password'];
        }

        if( isset($params['prefix']) )
        {
            $this->prefix = $params['prefix'];
        }

		parent::__construct('mysql:' . implode(';', $dns), $user, $password);
    }

/**
 * Query-quoted field name.
 * 
 * @param string $name Field name.
 * @return string Quoted name.
 */
    public function fieldName($name)
    {
        return '`' . $name . '`';
    }

/**
 * LIMIT/OFFSET clause for queries.
 * 
 * @param int|bool $limit Limit of rows to be affected by query (false if no limit).
 * @param int|bool $offset Number of rows to be skipped before applying query effects (false if no offset).
 * @return string LIMIT/OFFSET SQL clause for query.
 */
    public function limit($limit = false, $offset = false)
    {
        // by default this is empty part
        $sql = '';

        if($limit !== false)
        {
            $sql = ' LIMIT ';

            // OFFSET has no effect if there is no LIMIT
            if($offset !== false)
            {
                $sql .= $offset . ', ';
            }

            $sql .= $limit;
        }

        return $sql;
    }
}

/**#@-*/

?>
