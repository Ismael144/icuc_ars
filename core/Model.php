<?php

namespace App\core;

use App\Database\Database;

class Model
{
    /**
     * Database object 
     *
     * @var Database
     */
    public Database $db;

    public string $tableName = "";

    public static Database $staticDB;

    public function initDB()
    {
        $this->db = new Database;
        return $this->db;
    }

    /**
     * Getter for model property
     *
     * @return \PDO
     */
    final public function usePDO(): \PDO
    {
        return $this->initDB()->getDb();
    }

    /**
     * This is the static version of PDO
     *
     * @return \PDO
     */
    public static function staticUsePDO(): \PDO
    {
        self::$staticDB = new Database;
        return self::$staticDB->getDb();
    }

    /**
     * Runs and executes SQL Statements 
     *
     * @param string $sql
     * @return array|bool|null
     */
    final public function runQuery(string $sql, bool $hasReturnValue = true, bool $multiple = false)
    {
        if (!strlen($sql)) {
            return null;
        }

        $stmt = $this->usePDO()->query($sql);
        if ($hasReturnValue) {
            $results = $multiple ? $stmt->fetchAll() : $stmt->fetch();
            return $results;
        }
        return true;
    }

    /**
     * This method counts the number of rows in a given table, you simply enter the key and value which 
     * you are fetching by
     * 
     * @param string $key The column name 
     * @param string $value The value you are getting by 
     * 
     * @return int Which is the row count
     */
    public function makeCountQueriesOfTable(string $key, string $value): int
    {
        $pdo = $this->usePDO();
        $stmt = $pdo->prepare("SELECT count(*) FROM {$this->tableName} WHERE $key = :value");
        $stmt->execute(["value" => $value]);
        $fetchedRow = $stmt->fetch();
        $rowCount = $fetchedRow['count(*)'];
        return $rowCount;
    }
    /**
     * Get record(s) by given column 
     * 
     * @param string $by This is the column name to get by 
     * @param string $value This is the value to search for 
     * @param bool $multiple If you need multiple results 
     * @param array<string> $what The columns to return
     * 
     * @return array<mixed>
     */
    public function getRecordsBy(string $by, mixed $value, bool $multiple = false, array $what = []): array | bool
    {
        $what = count($what) ? implode(", ", $what) : "*";
        $stmt = $this->usePDO()->prepare("SELECT $what FROM {$this->tableName} WHERE $by = ?");
        $stmt->execute([$value]);
        $record = !$multiple ? $stmt->fetch() : $stmt->fetchAll();
        return $record;
    }

    /**
     * Deletes items from given table
     * @param string $by Which is the column name
     * @param string $value Value to delete by 
     * 
     * @return true Always returns true
     */
    public function deleteItemFromTable(string $by, mixed $value): true
    {
        $stmt = $this->usePDO()->prepare("DELETE FROM $this->tableName WHERE $by = ?");
        $stmt->execute([$value]);
        return true;
    }

    /**
     * Returns the number of rows in a table
     *
     * @param string $tableName
     * @return integer|boolean
     */
    public function getTableRecordsCount($column = "*", $isDistinct = false): int|bool
    {
        $tableName = $this->tableName;
        $isDistinct = $isDistinct ? "DISTINCT" : "";
        $stmt = $this->usePDO()->query("SELECT  count({$isDistinct} {$column}) as records_count FROM $tableName");
        $count = $stmt->fetch();
        if ($stmt->rowCount()) {
            return $count['records_count'];
        }
        return false;
    }

    /**
     * Will filter strings from unwanted characters
     *
     * @param mixed $value
     * @return string
     */
    final public function filter(mixed $value)
    {
        return trim(mb_convert_encoding(htmlspecialchars(html_entity_decode($value)), "utf-8"));
    }

    /**
     * Runs insert prepare statements
     *
     * @param array $data
     * @param string $tableName should be associated, since the keys will act as the params, Make sure the array keys match with the column names 
     * @return ?bool
     */
    final public function doInsert(array $data)
    {
        $tableName = $this->tableName;

        if (!count($data) || empty($tableName)) {
            return null;
        }

        # Gets column names and then turns it into comma values
        $columnNames = implode(", ", array_keys($data));

        # Gets prepare names(:name) and then turns it into comma values
        $prepareNames = [];
        foreach (array_keys($data) as $prepareName) {
            $prepareNames[] = ":$prepareName";
        }

        $prepareNames = implode(", ", $prepareNames);

        # Performing the query
        $sqlStmt = "INSERT INTO $tableName($columnNames) VALUES($prepareNames)";
        $stmt = $this->usePDO()->prepare($sqlStmt);

        $stmt->execute($data);

        return true;
    }

    /**
     * Fetches All Data from database
     *
     * @param string $tableName
     * @return array|null
     */
    final public function fetchAllData(): array|null
    {
        $tableName = $this->tableName;

        $query = $this->usePDO()->query("SELECT * FROM $tableName");
        $results = $query->fetchAll();
        return $results;
    }

    /**
     * Break down an array inorder to generate the where clause in SQL Statement
     * 
     * @param array<string, mixed> $where
     * @return string 
     */
    private function parseSQLWhereClauseToPreparedFormat(array $where)
    {
        $fieldsString = "";

        if (count($where)) {
            foreach (array_keys($where) as $i => $key) {
                $explodedKey = explode(".", $key);
                $spaceAtLast = $i == count($where) - 1 ? "" : ", ";
                if ($i == 0) {
                    $fieldsString .= "{$explodedKey[0]} = ? ";
                    continue;
                }

                if (count($explodedKey) > 1) {
                    $logicSymbol = $explodedKey[1];

                    $logicWord = "";

                    if ($logicSymbol == "|") {
                        $logicWord = "OR";
                    } else if ($logicSymbol == "&") {
                        $logicWord = "AND";
                    } else continue;

                    $negator = isset($explodedKey[2]) ? $explodedKey[2] : "";
                    # | or &
                    $fieldsString .= "$logicWord {$explodedKey[0]} {$negator}= ?{$spaceAtLast}";
                }
            }
        }

        return $fieldsString;
    }

    /**
     * Execute a prepared select statement without writing the actual query
     * 
     * @param array<string, mixed> $select 
     * @param array<string, mixed> $where
     * @param bool $single Specifies whether you want one record or multiple
     * 
     * @deprecated Use the runPreparedQuery instead, this method may contain bugs, so use wisely
     * 
     * @return array
     */
    public function preparedSelect(array $select = [], ?array $where = [], $single = true): array
    {
        $selectedFields = count($select) ? implode(", ", $select) : "*";

        if (is_null($where)) {
            $whereClause = "";
        } else {
            $whereClause = " WHERE {$this->parseSQLWhereClauseToPreparedFormat($where)}";
        }

        $stmt = $this->usePDO()->prepare("SELECT $selectedFields FROM {$this->tableName}{$whereClause}");
        echo "SELECT $selectedFields FROM {$this->tableName}{$whereClause}";
        if (!is_null($where)) $stmt->execute(array_values($where));  # Use $where directly for placeholders 
        else $stmt->execute();

        if ($single) {
            $response = $stmt->fetch();
            return is_bool($response) ? [] : $response;
        } else {
            return $stmt->fetchAll();
        }
    }

    /**
     * Run a prepared update without writing the actual statement
     * 
     * @param array<string, mixed> $set The array key is the parameter while the value is the value to be used
     * @param array<string, mixed> $where 
     * 
     * @return bool 
     */
    public function preparedUpdate(array $set, array $where): bool
    {
        $setClause = "";
        $whereClause = $this->parseSQLWhereClauseToPreparedFormat($where);

        foreach (array_keys($set) as $i => $key) {
            $ifComma = $i == count($set) - 1 ? "" : ", ";
            $setClause .= "$key = ?{$ifComma}";
        }

        $stmt = $this->usePDO()->prepare("UPDATE `{$this->tableName}` SET {$setClause} WHERE {$whereClause}");
        $stmt->execute([...array_values($set), ...array_values($where)]);

        return true;
    }

    /**
     * Fetch specific columns in a given table
     * 
     * @param ?array<string> $columns
     */
    public function fetchColumns(?array $columns = null): ?array
    {
        if (is_null($columns)) $columns = [];
        $data = $this->preparedSelect(select: $columns, where: [], single: false);
        return $data;
    }

    /**
     * A Prepared Delete without writing actual query
     * 
     * @param array<string, mixed> $where 
     * @return bool
     */
    public function preparedDelete(array $where)
    {
        $whereClause = $this->parseSQLWhereClauseToPreparedFormat($where);

        $stmt = $this->usePDO()->prepare("DELETE FROM {$this->tableName} WHERE $whereClause");
        $stmt->execute(array_values($where));

        return true;
    }

    /**
     * Paginates all data that comes from a table
     * 
     * @param int $totalItemsPerPage Decide number of items to display per page
     * @param int $pageNumber Enter the page number to display
     * @param array<string, int|string> options Specify optins like isDistinct: <bool> and orderBy: 'columnName ASC or DESC'
     * @param array<string> $columns Specify the columns to select 
     * @param array<string, mixed> $where Columns and values to be in the where clause
     * @param array<string, mixed> $search Specify key=>value items to search for, where the key is the column and the value is the value to search for
     * 
     */
    public function paginateData(int $totalItemsPerPage = 10, ?int $pageNumber = 1, $options = ["isDistinct" => false, "orderBy" => "date_created ASC"], $columns = ["*"], array $where = [], $search = []): array
    {
        $pdo = $this->usePDO();

        // Checking whether isDistinct is set 
        $isDistinct = isset($options["isDistinct"]) ? ($options["isDistinct"] ? "DISTINCT" : "") : "";
        $orderBy = isset($options["orderBy"]) ? "ORDER BY " . $options["orderBy"] : "ORDER BY date_created ASC";

        $searchQuery = "";

        // Search Logic Here 
        if (count($search)) {
            $operation = "OR";
            if (isset($search["operation"])) {
                $operation = $search["operation"];
                unset($search["operation"]);
            }

            $searchQuery = "WHERE ";
            $counter = 0;

            foreach ($search as $field => $searchTerm) {
                $searchQuery .= " $field LIKE '%$searchTerm%' ";
                $counter++;

                if ($counter != count($search)) $searchQuery .= $operation;
            }
        }

        // Get the number of items in a given table
        $modelTableCount = $this->getTableRecordsCount(column: $columns[0], isDistinct: $isDistinct);
        $totalItems = $modelTableCount;

        // Pagination Logic
        $totalPages = ceil($totalItems / $totalItemsPerPage);
        $currentPage = !is_null($pageNumber) && $totalPages >= $pageNumber && $pageNumber != 0 ? $pageNumber : 1;
        $offset = ($currentPage - 1) * $totalItemsPerPage;
        $columns = implode(", ", $columns);
        $isDistinct = $isDistinct ? "DISTINCT" : "";

        $whereClauseKey = "";

        $whereClause = "";

        if (count($where)) {
            foreach ($where as $key => $value) {
                $whereClauseKey = $key;
                $whereClause = "WHERE $key = :$key";
            }
        }

        $result = $pdo->prepare("SELECT {$isDistinct} {$columns} FROM {$this->tableName} {$whereClause} {$searchQuery} {$orderBy} LIMIT :offset, :items_per_page");
        $result->bindParam(':offset', $offset, \PDO::PARAM_INT);
        $result->bindParam(':items_per_page', $totalItemsPerPage, \PDO::PARAM_INT);

        if (count($where)) $result->bindParam(":$whereClauseKey", $where[$whereClauseKey], \PDO::PARAM_INT);

        $result->execute();

        $items = $result->fetchAll();

        $previousPage = 0;
        $nextPage = 0;

        if ($currentPage > 1) $previousPage = $currentPage - 1;

        if ($currentPage < $totalPages) $nextPage = $currentPage + 1;

        return [
            "data" => $items,
            "offset" => $offset,
            "next_page" => $nextPage,
            "total_pages" => $totalPages,
            "current_page" => $currentPage,
            "previous_page" => $previousPage,
            "total_entries" => count($items),
            "items_on_page" => $totalItemsPerPage == $offset ? ($totalItemsPerPage - $offset) + 1 : $totalItemsPerPage - $offset,
            "items_per_page" => $totalItemsPerPage,
        ];
    }

    /**
     * Search for records in the database 
     * 
     * @param string $searchTerm 
     * @param array<string> $searchColumns Enter table columns to be searched for
     * @param array<string> $columns The Columns to be displayed
     * @param bool $isDistinct IF you need your results to be distinct 
     * 
     * @return array<string, mixed>
     * 
     */
    public function search(string $searchTerm, $searchColumns = [], $columns = [], $isDistinct = false, $optionalSearch = true): array
    {
        $conditionalLogicSentence = "";
        $conditionalLogic = $optionalSearch ? "OR" : "AND";

        foreach ($searchColumns as $id => $column) {
            $ifConditionalLogic = $id != 0 ? $conditionalLogic : "";
            $conditionalLogicSentence .= " {$ifConditionalLogic} $column LIKE '%$searchTerm%'";
        }

        $columns = empty($columns) ? "*" : implode(", ", $columns);
        $isDistinct = $isDistinct ? "DISTINCT" : "";

        $statement = $this->usePDO()->prepare("SELECT {$isDistinct} {$columns} FROM {$this->tableName} WHERE {$conditionalLogicSentence}");
        $statement->execute();
        $searchData = $statement->fetchAll();

        return $searchData;
    }

    /**
     * Runs Prepared Queries
     *
     *  ***SQL Example***
     * ```sql
     *  SELECT * FROM [model_table] WHERE id = :id
     * -- Make sure the id matches the parameter name in the sql statement
     * -- [model_table] put it exactly the way it is, the method will automatically replace it with the real model table name
     * Note: use PDO name parameters `id = :id`
     * ```
     * 
     * @param string $sql
     * @param array $parameters must be associated, where name => value, make sure the name matches the one in the statement
     * 
     * @return array|bool 
     */
    public function runPreparedQuery(string $sqlStatement, array $parameters = [], $hasReturnValue = false, $multiple = false): array | bool
    {
        // Processing the SQL Statement (replacing [model_table] with actual tableName)
        $sqlStatement = str_replace("[model_table]", $this->tableName, $sqlStatement);
        $stmt = $this->usePDO()->prepare($sqlStatement);
        $stmt->execute($parameters);


        if ($hasReturnValue) {
            $results = $multiple ? $stmt->fetchAll() : $stmt->fetch();
            return $results;
        }

        return true;
    }
}
