<?php

/**#@+
 * @version 0.0.4
 * @since 0.0.4
 */

/**
 * @package POT
 * @version 0.1.3
 * @author Wrzasq <wrzasq@gmail.com>
 * @copyright 2007 - 2008 (C) by Wrzasq
 * @license http://www.gnu.org/licenses/lgpl-3.0.txt GNU Lesser General Public License, Version 3
 */

/**
 * ODBC connection interface.
 * 
 * <p>
 * At all everything that you really need to read from this class documentation is list of parameters for driver's constructor.
 * </p>
 * 
 * @package POT
 * @version 0.1.3
 */
class OTS_DB_ODBC extends OTS_Base_DB
{
/**
 * Creates database connection.
 * 
 * <p>
 * Connects to ODBC data source on given arguments.
 * </p>
 * 
 * <p>
 * List of parameters for this drivers:
 * </p>
 * 
 * <ul>
 * <li><var>host</var> - database host.</li>
 * <li><var>port</var> - ODBC driver.</li>
 * <li><var>database</var> - database name.</li>
 * <li><var>user</var> - user login.</li>
 * <li><var>password</var> - user password.</li>
 * <li><var>source</var> - ODBC data source.</li>
 * </ul>
 * 
 * <p>
 * Note: Since 0.1.3 version <var>source</var> parameter was added.
 * </p>
 * 
 * @version 0.1.3
 * @param array $params Connection parameters.
 * @throws PDOException On PDO operation error.
 */
    public function __construct($params)
    {
        $user = null;
        $password = null;
        $dns = array();

        if( isset($params['host']) )
        {
            $dns[] = 'HOSTNAME={' . $params['host'] . '}';
        }

        if( isset($params['port']) )
        {
            $dns[] = 'DRIVER={' . $params['port'] . '}';
        }

        if( isset($params['database']) )
        {
            $dns[] = 'DATABASE={' . $params['database'] . '}';
        }

        if( isset($params['user']) )
        {
            $user = $params['user'];
            $dns[] = 'UID={' . $user . '}';
        }

        if( isset($params['password']) )
        {
            $password = $params['password'];
            $dns[] = 'PWD={' . $user . '}';
        }

        if( isset($params['prefix']) )
        {
            $this->prefix = $params['prefix'];
        }

        // composes DNS
        $dns = implode(';', $dns);

        // source parameter overwrites all other params
        if( isset($params['source']) )
        {
            $dns = $params['source'];
        }

        // PDO constructor
        parent::__construct('odbc:' . $dns, $user, $password);
    }
}

/**#@-*/

?>
