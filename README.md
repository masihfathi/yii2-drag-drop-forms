FormBuilder module for Yii2
===================

## Features


1. Generate forms, surveys, polls, questionnaires (class FormBuilder)
 * Drag and drop - Sorting, editing, and deleting items
 * CRUD operations by AJAX
 * Built-in RBAC component

 
2. Form render  widget (class Form)
 * Validation forms (dynamic model)

3. Storage data submitted from form in databases
 * List of forms (GridView)
 * Create database tables after create form 
 * Delete database tables after delete form
 * Add table column after add field to form 
 * Rename table column after change the name of field
 * Drop table column after delete field in form

Added Feature:
* Edit, Delete, Preview of submitted item data
* Search and filter submitted item data
* after submit event for forms data to use form for payments or ...

## Installation Form Builder
```
composer require masihfathi/yii2-drag-drop-forms "dev-master"
```

## Configuration Form Builder
Make sure that you have properly configured `db` application component in config file and run the following command:
```bash
$ php yii migrate/up --migrationPath=@vendor/masihfathi/yii2-drag-drop-forms/migrations
```

Add the following code in your configuration file:
```php
'modules' => [
    'forms' => [
          'class' => 'masihfathi\form\Module',
     ],
]
```

##  Usage
URLs for the translating tool:

```
/forms/module/index                    // List of all forms                     
/forms/module/user                     // List of user forms
/forms/module/view                     // Preview form
/forms/module/create                   // FormBuilder - create form
/forms/module/update                   // Update form 
/forms/module/delete                   // Delete form
```

## Full example configuration Form Builder

```
'modules' => [
      'forms' => [
          'class' => 'masihfathi\form\Module',
          'db' => 'db',
          'formsTable' => '{{%forms}}',
          'formDataTable' => 'form_', // dont use prefix please
          'sendEmail' => true, 
          'emailSender' => 'info@email.net',
          'rules' => [
                [
                    'actions' => [ 'update', 'delete', 'clone','deletemultiple','preview','update-item'],
                    'allow' => true,
                    'roles' => ['updateOwnForm'],   // rule only owner can edit form
                ],
                [
                    'actions' => ['user', 'create'],
                    'allow' => true,
                    'roles' => ['user'],            // role only authenticated user can
                ]
            ],
          'controllerMap' => [
            'module' => [
              'class' => '\masihfathi\form\controllers\ModuleController',
              'on afterSubmit'=>function($event){
                    // code
                }
            ]
          ]
      ]
],
```

## Form renderer widget
```
use masihfathi\form\Form;
echo Form::widget([
     'body' => '[[{"field": "input", "type": "text", "width": "col-md-5", "name": "email", "placeholder": "email"},{"field": "input", "name": "pass", "type": "text", "placeholder": "pass", "width": "col-md-5"},{"field": "submit", "width": "col-md-2", "backgroundcolor": "btn-info", "label": "Submit"}]]',
     'typeRender' => 'php'
     ]);
```
or
```
  echo Form::widget([
     'formId' => 1, // equivalennt 'form' => FormModel::findOne(1)->body
  ]);
```

## Configure RBAC Component
Before you can go on you need to create those tables in the database.

```
php yii migrate --migrationPath=@yii/rbac/migrations
```

Building autorization data

To use generator console, add fallowing code to config console file
```
'controllerMap' => [
  'formsrbac' => [
      'class' => 'masihfathi\form\migrations\RbacController',
  ],
],
```
Create rbac tables in the database
```
php yii migrate --migrationPath=@yii/rbac/migrations
```
Create rules and roles for form module
```
php yii formsrbac/generate
```

## Tests
For tests run 
```
composer exec -v -- codecept -c vendor/masihfathi/yii2-drag-drop-forms run
```
or
```
cd vendor/masihfathi/yii2-drag-drop-forms
codecept run
```
## dependency
```
    "require": {
        "php": ">=7.0.0",
	"yiisoft/yii2": "~2.0.0",
        "kartik-v/yii2-detail-view": "*",
        "kartik-v/yii2-grid": "*"
    }
```