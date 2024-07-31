<?php
// @formatter:off
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator app\generators\model\MyModelGenerator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $properties string[] list of attribute labels (name => label) */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $generator->baseModelNamespace ?>;

use Yii;
use <?= $generator->commonModelNamespace.'\\'. $generator->baseModelClass ?>;

/**
* This is the base model class for table "<?= $generator->generateTableName($tableName) ?>".
*
<?php foreach ($properties as $column => $property):
    $column = (object)$property;
?>
* @property <?= "{$column->type} \${$column->name}\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
*
<?php foreach ($relations as $name => $relation): ?>
<?php if (!in_array($name, $generator->skippedRelations)): ?>
* @property <?= $relation[$generator::REL_CLASS] . ($relation[$generator::REL_IS_MULTIPLE] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
<?php endif; ?>
<?php endforeach; ?>
<?php endif; ?>
*/
class <?= $className ?> extends <?= ltrim($generator->baseModelClass, '\\') . "\n" ?>
{
<?php if ($generator->db !== 'db'): ?>
    /**
    * @return \yii\db\Connection the database connection used by this AR class.
    */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
    * @inheritdoc
    */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }

    /**
    * @inheritdoc
    */
    public function rules()
    {
        return [<?= "\n            " . implode(",\n            ", $rules) . "\n        " ?>];
    }

    /**
    * @inheritdoc
    */
    public function attributeLabels()
    {
        return [
<?php foreach ($labels as $name => $label): ?>
<?php if (!in_array($name, $generator->skippedColumns)): ?>
            <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
<?php endif; ?>
<?php endforeach; ?>
        ];
    }
<?php foreach ($relations as $name => $relation): ?>
<?php if (!in_array($name, $generator->skippedRelations)): ?>
    /**
    * Record relations here
    * @return yii\db\ActiveQuery
    */
    public function get<?= ucfirst($name) ?>()
    {
        <?= $relation[0] . "\n" ?>
    }
<?php endif; ?>
<?php endforeach; ?>

<?php if ($queryClassName): ?>
    <?php
    $queryClassFullName = '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
    ?>
    /**
    * @inheritdoc
    * @return <?= $queryClassFullName ?> the active query used by this AR class.
    */
    public static function find()
    {
        return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}
