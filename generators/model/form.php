<?php
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator app\generators\model\MyModelGenerator */
?>
<blockquote class="alert-warning" style="font-size: small">
    <strong>Note : </strong><br/>
    To generate nested or tree, please use <a href="http://demos.krajee.com/tree-manager#prepare-database">kartik-v\yii2-tree-manager</a>
    for table structure<br/>
    <strong>If table contains all the defined columns, the generator will automatically generate model that
        extends </strong><code>\kartik\tree\models\Tree</code>
</blockquote>
<?php

echo $form->field($generator, 'db');
echo $form->field($generator, 'tableName')->textInput(['table_prefix' => $generator->getTablePrefix()]);
echo $form->field($generator, 'skipTables');
echo $form->field($generator, 'standardizeCapitals')->checkbox();
echo $form->field($generator, 'singularize')->checkbox();
echo $form->field($generator, 'messageCategory');

echo $form->field($generator, 'nsModel');
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'generateBaseOnly')->checkbox();
echo $form->field($generator, 'baseModelNamespace');
echo $form->field($generator, 'baseModelClass');
echo $form->field($generator, 'generateQuery')->checkbox();
echo $form->field($generator, 'queryNs');
echo $form->field($generator, 'queryBaseClass');


echo $form->field($generator, 'generateRelations')->dropDownList([
    $generator::RELATIONS_NONE => 'No relations',
    $generator::RELATIONS_ALL => 'All relations',
    $generator::RELATIONS_ALL_INVERSE => 'All relations with inverse',
]);

echo $form->field($generator, 'optimisticLock');
echo $form->field($generator, 'skippedRelations');

echo $form->field($generator, 'generateAttributeHints')->checkbox();
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'useTablePrefix')->checkbox();

?>
