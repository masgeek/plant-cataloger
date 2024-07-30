<?php

namespace app\generators\model;

use mootensai\enhancedgii\model\Generator;
use Yii;
use yii\base\NotSupportedException;
use yii\db\Schema;
use yii\db\TableSchema;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 *
 * @property-read string $tablePrefix
 */
class MyModelGenerator extends Generator
{


    public $db = 'db';
    public $nsModel = 'app\models\base';
    public $queryNs = 'app\models\query';
    public $baseModelNs = 'app\common\models';
    public $baseModelClass = 'BaseModel';
    public $standardizeCapitals = false;
    public $useSchemaName = true;
    public $singularize = false;
    public $generateLabelsFromComments = true;
    public $generateQuery = false;
    public $useTablePrefix = true;
    public $optimisticLock = null;


    public function rules()
    {
        $rules = parent::rules();
        $rules[] = [['baseModelNs'], 'required'];
        $rules[] = [['baseModelClass'], 'match',
            'pattern' => '/^(\w+\.)?([\w\*]+)$/',
            'message' => 'Only word characters are allowed.'];

        $rules[] = [['singularize', 'useSchemaName', 'standardizeCapitals'], 'safe'];
        return $rules;
    }

    public function stickyAttributes()
    {
        $sticky = parent::stickyAttributes();
        $sticky[] = 'singularize';
        return $sticky;
    }

    public function getName()
    {
        return "Masgeek Generator (Model)";
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();
        $labels['standardizeCapitals'] = 'Standardize Capitals';
        $labels['singularize'] = 'Singularize';
        return $labels;
    }

    /**
     * @inheritdoc
     */
    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        $this->nameAttribute = ($this->nameAttribute) ? explode(',', str_replace(' ', '', $this->nameAttribute)) : [];
        $this->skippedColumns = ($this->skippedColumns) ? explode(',', str_replace(' ', '', $this->skippedColumns)) : [];
        $this->skippedRelations = ($this->skippedRelations) ? explode(',', str_replace(' ', '', $this->skippedRelations)) : [$this->skippedRelations];
        $this->skippedColumns = array_filter($this->skippedColumns);
        $this->skippedRelations = array_filter($this->skippedRelations);

        foreach ($this->getTableNames() as $tableName) {
            // model:
            if (strpos($this->tableName, '*') !== false) {
                $modelClassName = $this->generateClassName($tableName);
            } else {
                $modelClassName = (!empty($this->modelClass)) ? $this->modelClass : Inflector::id2camel($tableName, '_');
                if ($this->singularize) {
                    $modelClassName = Inflector::singularize($modelClassName);
                }
            }

            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableRelations = isset($relations[$tableName]) ? $relations[$tableName] : [];
            $tableSchema = $db->getTableSchema($tableName);
            $this->modelClass = "{$this->nsModel}\\{$modelClassName}";
            $this->tableSchema = $tableSchema;
            $this->isTree = !array_diff(self::getTreeColumns(), $tableSchema->columnNames);

            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'properties' => $this->generateProperties($tableSchema),
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => $tableRelations,
                'isTree' => $this->isTree
            ];

            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->nsModel)) . '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            // query :
            if ($queryClassName) {
                $params = [
                    'className' => $queryClassName,
                    'modelClassName' => $modelClassName,
                ];
                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $this->queryNs)) . '/' . $queryClassName . '.php', $this->render('query.php', $params)
                );
            }

            if (strpos($this->tableName, '*') !== false) {
                $this->modelClass = '';
            } else {
                $this->modelClass = $modelClassName;
            }
        }
        $this->nameAttribute = (is_array($this->nameAttribute)) ? implode(', ', $this->nameAttribute) : '';
        $this->skippedColumns = (is_array($this->skippedColumns)) ? implode(', ', $this->skippedColumns) : '';
        $this->skippedRelations = (is_array($this->skippedRelations)) ? implode(', ', $this->skippedRelations) : '';

        return $files;
    }

    /**
     * @return array the table names that match the pattern specified by [[tableName]].
     */
    protected function getTableNames()
    {
        if ($this->tableNames !== null) {
            return $this->tableNames;
        }
        $db = $this->getDbConnection();
        if ($db === null) {
            return [];
        }
        $tableNames = [];
        if (strpos($this->tableName, '*') !== false) {
            if (($pos = strrpos($this->tableName, '.')) !== false) {
                $schema = substr($this->tableName, 0, $pos);
                $pattern = '/^' . str_replace('*', '\w+', substr($this->tableName, $pos + 1)) . '$/';
            } else {
                $schema = '';
                $pattern = '/^' . str_replace('*', '\w+', $this->tableName) . '$/';
            }

            foreach ($db->schema->getTableNames($schema) as $table) {
                if (preg_match($pattern, $table)) {
                    $tableNames[] = $schema === '' ? $table : ($schema . '.' . $table);
                }
            }
        } elseif (($table = $db->getTableSchema($this->tableName, true)) !== null) {
            $tableNames[] = $this->tableName;
            $this->classNames[$this->tableName] = $this->modelClass;
        }

        return $this->tableNames = $tableNames;
    }

    /**
     * Generates a class name from the specified table name.
     * @param string $tableName the table name (which may contain schema prefix)
     * @param bool $useSchemaName should schema name be included in the class name, if present
     * @return string the generated class name
     */
    protected function generateClassName($tableName, $useSchemaName = null)
    {

        if (isset($this->classNames[$tableName])) {
            return $this->classNames[$tableName];
        }
        $schemaName = '';

        $fullTableName = $tableName;

        if (($pos = strrpos($tableName, '.')) !== false) {
            if (($useSchemaName === null && $this->useSchemaName) || $useSchemaName) {
                $schemaName = substr($tableName, 0, $pos) . '_';
            }
            $tableName = substr($tableName, $pos + 1);
        }


        $db = $this->getDbConnection();
        $patterns = [];
        $patterns[] = "/^{$db->tablePrefix}(.*?)$/";
        $patterns[] = "/^(.*?){$db->tablePrefix}$/";
        if (strpos($this->tableName, '*') !== false) {
            $pattern = $this->tableName;
            if (($pos = strrpos($pattern, '.')) !== false) {
                $pattern = substr($pattern, $pos + 1);
            }
            $patterns[] = '/^' . str_replace('*', '(\w+)', $pattern) . '$/';
        }

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $tableName, $matches)) {
                $className = $matches[1];
                break;
            }
        }


        if ($this->standardizeCapitals) {
            $schemaName = ctype_upper(preg_replace('/[_-]/', '', $schemaName)) ? strtolower($schemaName) : $schemaName;
            $className = ctype_upper(preg_replace('/[_-]/', '', $className)) ? strtolower($className) : $className;
            $this->classNames[$fullTableName] = Inflector::camelize(Inflector::camel2words($schemaName . $className));
        } else {
            $this->classNames[$fullTableName] = Inflector::id2camel($schemaName . $className, '_');
        }

        if ($this->singularize) {
            $this->classNames[$fullTableName] = Inflector::singularize($this->classNames[$fullTableName]);
        }

        return $this->classNames[$fullTableName];
    }

    /**
     * Returns the `tablePrefix` property of the DB connection as specified
     *
     * @return string
     * @since 2.0.5
     * @see getDbConnection
     */
    public function getTablePrefix()
    {
        $db = $this->getDbConnection();
        if ($db !== null) {
            return $db->tablePrefix;
        }

        return '';
    }

    /**
     * Generates validation rules for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated validation rules
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                if ($this->isTree && in_array($column->name, ['lft', 'rgt', 'lvl'])) {

                } else {
                    $types['required'][] = $column->name;
                }
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_TINYINT:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }
        $rules = [];
        foreach ($types as $type => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length]";
        }

        // Unique indexes rules
        try {
            $db = $this->getDbConnection();
            $uniqueIndexes = $db->getSchema()->findUniqueIndexes($table);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount == 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique']";
                    } elseif ($attributesCount > 1) {
                        $labels = array_intersect_key($this->generateLabels($table), array_flip($uniqueColumns));
                        $lastLabel = array_pop($labels);
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['" . $columnsList . "'], 'unique', 'targetAttribute' => ['" . $columnsList . "'], 'message' => 'The combination of " . implode(', ', $labels) . " and " . $lastLabel . " has already been taken.']";
                    }
                }
            }
            if (!empty($this->optimisticLock)) {
                $rules[] = "[['" . $this->optimisticLock . "'], 'default', 'value' => '0']";
                $rules[] = "[['" . $this->optimisticLock . "'], 'mootensai\\components\\OptimisticLockValidator']";
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        return $rules;
    }

    /**
     * Generates the properties for the specified table.
     * @param \yii\db\TableSchema $table the table schema
     * @return array the generated properties (property => type)
     * @since 2.0.6
     */
    public function generateProperties($table)
    {
        $properties = [];
        foreach ($table->columns as $column) {
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                    $type = 'integer';
                    break;
                case Schema::TYPE_BOOLEAN:
                case Schema::TYPE_TINYINT:
                    $type = 'boolean';
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $type = 'float';
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_JSON:
                    $type = 'string';
                    break;
                default:
                    $type = $column->phpType;
            }
            if ($column->allowNull) {
                $type .= '|null';
            }
            $properties[$column->name] = [
                'type' => $type,
                'name' => $column->name,
                'comment' => $column->comment,
            ];
        }

        return $properties;
    }

    /**
     * @return array the generated relation declarations
     * @throws yii\base\NotSupportedException
     */
    protected function generateRelations()
    {
        if (!$this->generateRelations === self::RELATIONS_NONE) {
            return [];
        }

        $db = $this->getDbConnection();

        $relations = [];
        foreach ($this->getSchemaNames() as $schemaName) {
            foreach ($db->getSchema()->getTableSchemas($schemaName) as $table) {
                $className = $this->generateClassName($table->fullName);
                foreach ($table->foreignKeys as $refs) {
                    $refTable = $refs[0];
                    $refTableSchema = $db->getTableSchema($refTable);
                    if ($refTableSchema === null) {
                        // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                        continue;
                    }
                    unset($refs[0]);
                    $fks = array_keys($refs);
                    $refClassName = $this->generateClassName($refTable);

                    // Add relation for this table
                    $link = $this->generateRelationLink(array_flip($refs));
                    $relationName = $this->generateRelationName($relations, $table, $fks[0], false);
                    $relFK = key($refs);
                    $relations[$table->fullName][lcfirst($relationName)] = [
                        self::REL_TYPE => "return \$this->hasOne(\\{$this->nsModel}\\$refClassName::class, $link);", // relation type
                        self::REL_CLASS => $refClassName, //relclass
                        self::REL_IS_MULTIPLE => 0, //is multiple
                        self::REL_TABLE => $refTable, //related table
                        self::REL_PRIMARY_KEY => $refs[$relFK], // related primary key
                        self::REL_FOREIGN_KEY => $relFK, // this foreign key
                        self::REL_IS_MASTER => in_array($relFK, $table->getColumnNames()) ? 1 : 0
                    ];

                    // Add relation for the referenced table
                    $hasMany = $this->isHasManyRelation($table, $fks);
                    $link = $this->generateRelationLink($refs);
                    $relationName = $this->generateRelationName($relations, $refTableSchema, $className, $hasMany);
                    $relations[$refTableSchema->fullName][lcfirst($relationName)] = [
                        self::REL_TYPE => "return \$this->" . ($hasMany ? 'hasMany' : 'hasOne') . "(\\{$this->nsModel}\\$className::class, $link);", // rel type
                        self::REL_CLASS => $className, //rel class
                        self::REL_IS_MULTIPLE => $hasMany, //is multiple
                        self::REL_TABLE => $table->fullName, // rel table
                        self::REL_PRIMARY_KEY => $refs[key($refs)], // rel primary key
                        self::REL_FOREIGN_KEY => key($refs), // this foreign key
                        self::REL_IS_MASTER => in_array($relFK, $refTableSchema->getColumnNames()) ? 1 : 0
                    ];
                }

                if (($junctionFks = $this->checkPivotTable($table)) === false) {
                    continue;
                }

                $relations = $this->generateManyManyRelations($table, $junctionFks, $relations);
            }
        }

        if ($this->generateRelations === self::RELATIONS_ALL_INVERSE) {
            return $this->addInverseRelations($relations);
        }

        return $relations;
    }

    /**
     * Generates relations using a junction table by adding an extra viaTable().
     * @param TableSchema $table
     * @param array $fks obtained from the checkPivotTable() method
     * @param array $relations
     * @return array modified $relations
     */
    private function generateManyManyRelations(TableSchema $table, array $fks, array $relations): array
    {
        $db = $this->getDbConnection();
        $table0 = $fks[$table->primaryKey[0]][0];
        $table1 = $fks[$table->primaryKey[1]][0];
        $className0 = $this->generateClassName($table0);
        $className1 = $this->generateClassName($table1);
        $table0Schema = $db->getTableSchema($table0);
        $table1Schema = $db->getTableSchema($table1);

        $link = $this->generateRelationLink([$fks[$table->primaryKey[1]][1] => $table->primaryKey[1]]);
        $viaLink = $this->generateRelationLink([$table->primaryKey[0] => $fks[$table->primaryKey[0]][1]]);
        $relationName = $this->generateRelationName($relations, $table0Schema, $table->primaryKey[1], true);
        $relations[$table0Schema->fullName][$relationName] = [
            "return \$this->hasMany(\\{$this->nsModel}\\$className1::class, $link)->viaTable('"
            . $this->generateTableName($table->name) . "', $viaLink);",
            $className1,
            true,
        ];

        $link = $this->generateRelationLink([$fks[$table->primaryKey[0]][1] => $table->primaryKey[0]]);
        $viaLink = $this->generateRelationLink([$table->primaryKey[1] => $fks[$table->primaryKey[1]][1]]);
        $relationName = $this->generateRelationName($relations, $table1Schema, $table->primaryKey[0], true);
        $relations[$table1Schema->fullName][$relationName] = [
            "return \$this->hasMany(\\{$this->nsModel}\\$className0::class, $link)->viaTable('"
            . $this->generateTableName($table->name) . "', $viaLink);",
            $className0,
            true,
        ];

        return $relations;
    }

}
