<?php
App::uses('CakeLog', 'Log');

class RciamStatsViewerUtils
{
    private $configData;

    public function __construct($configData = array())
    {
        $this->configData = $configData;
    }

    /**
     * @param $conn
     * @param $days
     * @param null $sp
     * @param null $idp
     * @return int
     */
    public function getTotalLoginCounts($conn, $days, $sp = NULL, $idp = NULL)
    {
        assert($conn !== NULL);
        $queryParams = array();  // Initialize
        $dbDriver = $this->configData['RciamStatsViewer']['type'];
        $table_name = $this->configData['RciamStatsViewer']['statisticsTableName'];

        if ($days === 0) {    // 0 = all time
            if ($dbDriver === 'PG') {
                if ($sp === null && $idp === null) {
                    $sql = "SELECT SUM(count) AS count FROM $table_name WHERE service != ''";
                } else if ($sp !== null) {
                    $sql = "SELECT SUM(count) AS count FROM $table_name WHERE service = '" . $sp . "'";
                } else if ($idp !== null) {
                    $sql = "SELECT SUM(count) AS count FROM $table_name WHERE sourceidp = '" . $idp . "'";
                }
            } else {
                $sql = "SELECT SUM(count) AS count FROM $table_name WHERE service != ''";
            }
        } else {
            if ($dbDriver === 'PG') {
                if ($sp === null && $idp === null) {
                    $sql = "SELECT SUM(count) AS count FROM $table_name WHERE service != '' AND CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ?";
                } else if ($sp !== null) {
                    $sql = "SELECT SUM(count) AS count FROM $table_name WHERE service = '" . $sp . "' AND CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ?";
                } else if ($idp !== null) {
                    $sql = "SELECT SUM(count) AS count FROM $table_name WHERE sourceidp = '" . $idp . "' AND CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ?";
                }
            } else {
                $sql = "SELECT SUM(count) AS count FROM $table_name WHERE service != '' AND CONCAT(year,'-',LPAD(month,2,'00'),'-',LPAD(day,2,'00')) BETWEEN CURDATE() - INTERVAL ? DAY AND CURDATE()";
            }
            $queryParams = array($days);
        }
        $result = $this->execQuery($conn, $sql, $queryParams);

        return !empty($result[0][0]['count']) ? $result[0][0]['count'] : 0;
    }

    /**
     * @param $conn
     * @param $days
     * @param null $sp
     * @return mixed
     */
    public function getLoginCountPerIdp($conn, $days, $sp = null, $dateFrom = null, $dateTo = null)
    {
        assert($conn !== NULL);
        $queryParams = array();  // Initialize
        $dbDriver = $this->configData['RciamStatsViewer']['type'];
        $tableName =  $this->configData['RciamStatsViewer']['statisticsTableName'];
        $identityProvidersMapTableName =  $this->configData['RciamStatsViewer']['identityProvidersMapTableName'];

        $subQuery = '';
        if($dateFrom != null && $dateTo != null && $dateTo > $dateFrom){ //ranges in datatable
            $subQuery = " CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) BETWEEN '". $dateFrom ."' AND '". $dateTo ."'";          
            if ($sp === null)
                $subQuery = " WHERE". $subQuery;
            else   
                $subQuery = " AND". $subQuery;
        }
        if ($days === 0) {    // 0 = all time
            if ($dbDriver === 'PG') {
                if ($sp === null) {
                    $sql = "SELECT sourceidp, COALESCE(name,sourceIdp) AS idpname, SUM(count) AS count FROM $tableName LEFT OUTER JOIN $identityProvidersMapTableName ON sourceidp = entityId $subQuery GROUP BY sourceidp, name HAVING sourceidp != '' ORDER BY count DESC";
                } else {
                    $sql = "SELECT sourceidp, COALESCE(name,sourceIdp) AS idpname, SUM(count) AS count FROM $tableName LEFT OUTER JOIN $identityProvidersMapTableName ON sourceidp = entityId WHERE service = '" . $sp . "' $subQuery GROUP BY sourceidp, name HAVING sourceidp != '' ORDER BY count DESC";
                }
            } else { // MYSQL
                if ($sp === null) {
                    $sql = "SELECT sourceidp, IFNULL(name,sourceIdp) AS idpname, SUM(count) AS count FROM $tableName LEFT OUTER JOIN $identityProvidersMapTableName ON sourceidp = entityId GROUP BY sourceidp, name HAVING sourceidp != '' ORDER BY count DESC";
                } else {
                    $sql = "SELECT sourceidp, IFNULL(name,sourceIdp) AS idpname, SUM(count) AS count FROM $tableName LEFT OUTER JOIN $identityProvidersMapTableName ON sourceidp = entityId WHERE service = '" . $sp . "' GROUP BY sourceidp, name HAVING sourceidp != '' ORDER BY count DESC";
                }
            }
        } else {
            if ($dbDriver === 'PG') {
                if ($sp === null) {
                    $sql = "SELECT sourceidp, COALESCE(name,sourceIdp) AS idpname, SUM(count) AS count FROM $tableName LEFT OUTER JOIN $identityProvidersMapTableName ON sourceidp = entityId WHERE CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ? GROUP BY sourceidp, idpname HAVING sourceidp != '' ORDER BY count DESC";
                } else {
                    $sql = "SELECT sourceidp, COALESCE(name,sourceIdp) AS idpname, SUM(count) AS count FROM $tableName LEFT OUTER JOIN $identityProvidersMapTableName ON sourceidp = entityId WHERE service='" . $sp . "' AND CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ? GROUP BY sourceidp, idpname HAVING sourceidp != '' ORDER BY count DESC";
                }
            } else { // MYSQL
                if ($sp === null) {
                    $sql = "SELECT sourceidp, IFNULL(name,sourceIdp) AS idpname, SUM(count) AS count FROM $tableName LEFT OUTER JOIN $identityProvidersMapTableName ON sourceidp = entityId WHERE CONCAT(year,'-',LPAD(month,2,'00'),'-',LPAD(day,2,'00')) BETWEEN CURDATE() - INTERVAL ? DAY AND CURDATE() GROUP BY sourceidp, idpname HAVING sourceidp != '' ORDER BY count DESC";
                } else {
                    $sql = "SELECT sourceidp, IFNULL(name,sourceIdp) AS idpname, SUM(count) AS count FROM $tableName LEFT OUTER JOIN $identityProvidersMapTableName ON sourceidp = entityId WHERE service='" . $sp . "' AND CONCAT(year,'-',LPAD(month,2,'00'),'-',LPAD(day,2,'00')) BETWEEN CURDATE() - INTERVAL ? DAY AND CURDATE() GROUP BY sourceidp, idpname HAVING sourceidp != '' ORDER BY count DESC";
                }
            }
            $queryParams = array(
                $days,
            );
        }
        return $this->execQuery($conn, $sql, $queryParams);
    }

    /**
     * @param $conn
     * @param $days
     * @param null $idp
     * @return mixed
     */
    public function getLoginCountPerSp($conn, $days, $idp = null, $dateFrom = null, $dateTo = null)
    {
        assert($conn !== NULL);
        $queryParams = array();  // Initialize
        $table_name =  $this->configData['RciamStatsViewer']['statisticsTableName'];
        $serviceProvidersMapTableName =  $this->configData['RciamStatsViewer']['serviceProvidersMapTableName'];
        $dbDriver = $this->configData['RciamStatsViewer']['type'];
       
        $subQuery = '';
        if($dateFrom != null && $dateTo != null && $dateTo > $dateFrom){ //ranges in datatable
            $subQuery = " CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) BETWEEN '". $dateFrom ."' AND '". $dateTo ."'";          
            if ($idp === null)
                $subQuery = " WHERE". $subQuery;
            else   
                $subQuery = " AND". $subQuery;
        }
        if ($days === 0) {    // 0 = all time
            if ($dbDriver === 'PG') {
                if ($idp === null) {
                    $sql = "SELECT service, COALESCE(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier $subQuery GROUP BY service, name HAVING service != ''  ORDER BY count DESC";
                } else {
                    $sql = "SELECT service, COALESCE(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier WHERE sourceidp = '" . $idp . "' $subQuery GROUP BY service, name HAVING service != ''  ORDER BY count DESC";
                }
            } else { // MYSQL
                if ($idp === null) {
                    $sql = "SELECT service, IFNULL(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN " . $serviceProvidersMapTableName . " ON service = identifier GROUP BY service HAVING service != ''  ORDER BY count DESC";
                } else {
                    $sql = "SELECT service, IFNULL(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN " . $serviceProvidersMapTableName . " ON service = identifier WHERE sourceidp = '" . $idp . "' GROUP BY service HAVING service != ''  ORDER BY count DESC";
                }
            }
        } else {
            if ($dbDriver === 'PG') {
                if ($idp === null) {
                    $sql = "SELECT service, COALESCE(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier WHERE CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ? GROUP BY service, spname HAVING service != ''  ORDER BY count DESC";
                } else {
                    $sql = "SELECT service, COALESCE(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier WHERE sourceidp = '" . $idp . "' AND CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ? GROUP BY service, spname HAVING service != ''  ORDER BY count DESC";
                }
            } else { //MYSQL
                if ($idp === null) {
                    $sql = "SELECT service, IFNULL(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier WHERE CONCAT(year,'-',LPAD(month,2,'00'),'-',LPAD(day,2,'00')) BETWEEN CURDATE() - INTERVAL :days DAY AND CURDATE() GROUP BY service, spname HAVING service != ''  ORDER BY count DESC";
                } else {
                    $sql = "SELECT service, IFNULL(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier WHERE sourceidp = '" . $idp . "' AND CONCAT(year,'-',LPAD(month,2,'00'),'-',LPAD(day,2,'00')) BETWEEN CURDATE() - INTERVAL :days DAY AND CURDATE() GROUP BY service, spname HAVING service != ''  ORDER BY count DESC";
                }
            }
            $queryParams = array(
                $days
            );
        }

        return $this->execQuery($conn, $sql, $queryParams);
    }

    /**
     * @param $conn
     * @param $days
     * @param $idpIdentifier
     * @param $type
     * @return mixed
     */
    public function getLoginCountPerDayForProvider($conn, $days, $identifier = NULL, $providerType = NULL)
    {
        $dbDriver = $this->configData['RciamStatsViewer']['type'];
        $queryParams = array();  // Initialize
        assert($conn !== NULL);
        $table_name =  $this->configData['RciamStatsViewer']['statisticsTableName'];
        if($providerType == 'idp'){
            $column = 'sourceidp = ?';
        }
        else if($providerType == 'sp'){
            $column = 'service = ?';
        }
        else if ($providerType == null){
            $column = 'service != ?';
            $identifier = '';
        }
        if ($days === 0) {    // 0 = all time
            if ($dbDriver === 'PG') {
                $sql = "SELECT year, month, day, SUM(count) AS count FROM $table_name WHERE $column GROUP BY year, month,day ORDER BY year DESC,month DESC,day DESC";
            } else {
                $sql = "SELECT year, month, day, SUM(count) AS count FROM $table_name WHERE $column GROUP BY year DESC,month DESC,day DESC";
            }
            $queryParams = array(
                $identifier
            );
        } else { // MYSQL
            if ($dbDriver === 'PG') {
                $sql = "SELECT year, month, day, SUM(count) AS count FROM $table_name WHERE $column AND CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ? GROUP BY year, month, day ORDER BY year DESC,month DESC,day DESC";
            } else {
                $sql = "SELECT year, month, day, SUM(count) AS count FROM $table_name WHERE $column AND CONCAT(year,'-',LPAD(month,2,'00'),'-',LPAD(day,2,'00')) BETWEEN CURDATE() - INTERVAL ? DAY AND CURDATE() GROUP BY year DESC,month DESC,day DESC";
            }
            $queryParams = array(
                $identifier,
                $days
            );
        }

        return $this->execQuery($conn, $sql, $queryParams);
    }
    
    /**
     * @param $conn
     * @param $days
     * @param $spIdentifier
     * @return mixed
     */
    public function getAccessCountForServicePerIdentityProviders($conn, $days, $spIdentifier)
    {
        $queryParams = array();  // Initialize
        assert($conn !== NULL);
        $table_name =  $this->configData['RciamStatsViewer']['statisticsTableName'];
        $identityProvidersMapTableName =  $this->configData['RciamStatsViewer']['identityProvidersMapTableName'];
        $dbDriver = $this->configData['RciamStatsViewer']['type'];

        if ($days === 0) {    // 0 = all time
            if ($dbDriver === 'PG') {
                $query = "SELECT sourceIdp, service, COALESCE(name,sourceIdp) AS idpname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $identityProvidersMapTableName ON sourceIdp = entityId GROUP BY sourceIdp, service, idpname HAVING sourceIdp != '' AND service = ? ORDER BY count DESC";
            } else { // MYSQL
                $query = "SELECT sourceIdp, service, IFNULL(name,sourceIdp) AS idpname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $identityProvidersMapTableName ON sourceIdp = entityId GROUP BY sourceIdp, service HAVING sourceIdp != '' AND service = ?  ORDER BY count DESC";
            }
            $queryParams = array(
                $spIdentifier
            );
        } else {
            if ($dbDriver === 'PG') {
                $query = "SELECT year, month, day, sourceIdp, service, COALESCE(name,sourceIdp) AS idpname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $identityProvidersMapTableName ON sourceIdp = entityId WHERE CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ? GROUP BY sourceIdp, service, idpname, year, month, day HAVING sourceIdp != '' AND service = ? ORDER BY count DESC";
            } else { // MYSQL
                $query = "SELECT year, month, day, sourceIdp, service, IFNULL(name,sourceIdp) AS idpname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $identityProvidersMapTableName ON sourceIdp = entityId WHERE CONCAT(year,'-',LPAD(month,2,'00'),'-',LPAD(day,2,'00')) BETWEEN CURDATE() - INTERVAL ? DAY AND CURDATE() GROUP BY sourceIdp, service HAVING sourceIdp != '' AND service = ? ORDER BY count DESC";
            }
            $queryParams = array(
                $days, $spIdentifier
            );
        }

        return $this->execQuery($conn, $query, $queryParams);
    }

    /**
     * @param $conn
     * @param $days
     * @param $idpEntityId
     * @return mixed
     */
    public function getAccessCountForIdentityProviderPerServiceProviders($conn, $days, $idpEntityId)
    {
        $dbDriver = $this->configData['RciamStatsViewer']['type'];
        $queryParams = array();  // Initialize
        assert($conn !== NULL);
        $table_name =  $this->configData['RciamStatsViewer']['statisticsTableName'];
        $serviceProvidersMapTableName =  $this->configData['RciamStatsViewer']['serviceProvidersMapTableName'];

        if ($days === 0) {    // 0 = all time
            if ($dbDriver === 'PG') {
                $query = "SELECT sourceIdp, service, COALESCE(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier GROUP BY sourceIdp, service, name HAVING service != '' AND sourceIdp = ? ORDER BY count DESC";
            } else { // MYSQL
                $query = "SELECT sourceIdp, service, IFNULL(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier GROUP BY sourceIdp, service HAVING service != '' AND sourceIdp = ? ORDER BY count DESC";
            }
            $queryParams = array(
                $idpEntityId,
            );
        } else {
            if ($dbDriver === 'PG') {
                $query = "SELECT year, month, day, sourceIdp, service, COALESCE(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier WHERE CAST(CONCAT(year,'-',LPAD(CAST(month AS varchar),2,'0'),'-',LPAD(CAST(day AS varchar),2,'0')) AS date) > current_date - INTERVAL '1 days' * ? GROUP BY sourceIdp, service, name, year, month, day HAVING service != '' AND sourceIdp = :idpEntityId ORDER BY count DESC";
            } else { // MYSQL
                $query = "SELECT year, month, day, sourceIdp, service, IFNULL(name,service) AS spname, SUM(count) AS count FROM $table_name LEFT OUTER JOIN $serviceProvidersMapTableName ON service = identifier WHERE CONCAT(year,'-',LPAD(month,2,'00'),'-',LPAD(day,2,'00')) BETWEEN CURDATE() - INTERVAL ? DAY AND CURDATE() GROUP BY sourceIdp, service HAVING service != '' AND sourceIdp = ? ORDER BY count DESC";
            }
            $queryParams = array(
                $days,
                $idpEntityId
            );
        }

        return $this->execQuery($conn, $query, $queryParams);
    }

    /**
     * @param $connection
     * @param $sql
     * @param array $queryParams
     * @return mixed
     */
    protected function execQuery($connection, $sql, $queryParams = array())
    {
        try {
            return $connection->fetchAll($sql, $queryParams);
        } catch (PDOException $e) {
            if (Configure::read('debug')) {
                CakeLog::write('error', __METHOD__ . ':: Database Action failed. Error Message::' . '[' . $e->getCode() . ']:' . $e->getMessage());
            }
            throw new RuntimeException($e->getCode());
        }
    }
}
