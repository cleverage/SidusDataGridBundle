## Usage

### Controller/Action side

````php
<?php
/**
 * @var \Symfony\Component\Form\FormFactoryInterface $formFactory 
 * @var \Sidus\DataGridBundle\Registry\DataGridRegistry $datagridRegistry
 */

// Create a form builder, configure it any way you want
$builder = $formFactory->createBuilder();

// Fetch you query handler using the registry
$datagrid = $datagridRegistry->getDataGrid('<datagrid_code>');

// Build the final form using your builder
$datagrid->buildForm($builder);

// Handle the request to apply filters from the form submission
$datagrid->handleRequest($request);
// Alternatively, you can use $datagrid->handleArray() to hydrate the form data from an array manually

// Bind these variables to your view
$viewParameters = [
    'datagrid' => $datagrid,
];
````

In real life, this is how it goes:
[https://github.com/VincentChalnot/SidusDataGridDemo/blob/master/src/AppBundle/Action/SearchAction.php](https://github.com/VincentChalnot/SidusDataGridDemo/blob/master/src/AppBundle/Action/SearchAction.php)

### Rendering side

Render the entire datagrid using configured template:

````twig
{{ render_datagrid(datagrid) }}
````

You can pass additional view parameters to the template as a second argument when using custom templates:
````twig
{{ render_datagrid(datagrid, {custom_option: 'custom_value'}) }}
````
These additional parameters will be available 

This example is feature in [the live demo](http://datagrid-demo.sidus.fr/).
