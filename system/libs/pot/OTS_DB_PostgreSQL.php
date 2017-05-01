<?php

/**#@+
 * @version 0.0.4
 * @since 0.0.4
 */

/**
 * @package POT
 * @version 0.1.3
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * PostgreSQL connection interface.
 * 
 * <p>
 * At all everything that you really need to read from this class documentation is list of parameters for driver's constructor.
 * </p>
 * 
 * @package POT
 * @version 0.1.3
 */
class OTS_DB_PostgreSQL extends OTS_Base_DB
{
/**
 * Creates database connection.
 * 
 * <p>
 * Connects to PgSQL (PostgreSQL) database on given arguments.
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

        // PDO constructor
        parent::__construct('pgsql:' . implode(' ', $dns), $user, $password);
    }
}

/**#@-*/

?>
