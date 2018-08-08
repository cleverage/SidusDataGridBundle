## Configuration

### Example

For this example, we will be using the ````doctrine```` data provider but the same principles applies for any data
provider.

Note that the ````query_handler```` configuration key contains a configuration that is documented in the
[Sidus/FilterBundle](https://github.com/VincentChalnot/SidusFilterBundle) and that is not going to be documented here.

````yaml
sidus_data_grid:
    configurations:
        news:
            # Define each column you want to display in your table
            columns:
                id:
                    label: '#' # Custom label
                title: ~
                publicationDate: ~ # Dates are automatically rendered using the IntlDateFormatter
                publicationStatus:
                    template: 'Datagrid/badge.html.twig' # Custom template to render a value
                author: # This will relate on the __toString() method of the related object
                    sort_column: author.label # Define a sort custom sort column
                categories: ~ # Arrays are imploded with ", "
                actions:
                    template: 'Datagrid/actions.html.twig'
            actions:
                # Define as many actions as you want, see dedicated section to see what's possible
                create:
                    form_type: Symfony\Component\Form\Extension\Core\Type\ButtonType
                    disabled: true
                    attr:
                        class: btn btn-primary

            # The query_handler configuration key must contain a valid Sidus/FilterBundle configuration definition
            # See https://github.com/VincentChalnot/SidusFilterBundle for more information
            query_handler:
                provider: doctrine
                options:
                    entity: AppBundle\Entity\News
                # ...
````

### Configuration full reference

Displayed values are default values unless specified otherwise. Note that the '@' prefix for service references is
optional.

````yaml
sidus_data_grid:
    # Change the form theme for filters and buttons for all datagrids
    default_form_theme: ~

    # Change the default template for all datagrids
    default_datagrid_template: SidusDataGridBundle:DataGrid:bootstrap4.html.twig

    # Change the default value renderer for all datagrids
    default_column_value_renderer: Sidus\DataGridBundle\Renderer\ColumnValueRendererInterface

    # Change the default label renderer for all datagrids
    default_column_label_renderer: Sidus\DataGridBundle\Renderer\ColumnLabelRendererInterface

    # Global actions, will only be used if a datagrid doesn't declare any action.
    actions: {} # Variable node

    # List of datagrid configurations
    configurations:
        <datagrid_code>:
            # See https://github.com/VincentChalnot/SidusFilterBundle
            # Can be either a full configuration or just the code of an already defined configuration
            query_handler: ~ # Required

            # Form theme for filters and buttons
            form_theme: ~ # Default to default_form_theme

            # Template used to render the datagrid
            template: ~ # Default to default_datagrid_template

            # Renderer service id used to render columns values
            column_value_renderer: ~ # Default to default_column_value_renderer

            # Renderer service id used to render column labels
            column_label_renderer: ~ # Default to default_column_label_renderer

            # Allow to use a parent configuration to merge it in yours
            parent: ~

            # Optional: This allows you to add custom buttons, links or form elements to your filter form
            actions:
                <action_name>:
                    form_type: Sidus\DataGridBundle\Form\Type\LinkType # This is the default form type
                    <form_option>: <mixed> # Any form option supported by the form type
                    # # List of form options...

                link_example: # This uses the default LinkType
                    label: This is a link
                    # You must either define a route or an uri.
                    route: ~
                    route_parameters: {}
                    # If you set this option, the route and route_parameters will be ignored
                    uri: ~
                    
                # This also allows you to create custom submit buttons
                button_example:
                    form_type: Symfony\Component\Form\Extension\Core\Type\SubmitType
                    label: Custom submit
                    # See http://symfony.com/doc/current/reference/forms/types/submit.html

            # Optional: Customize the submit button of the filters, this represents the default values
            submit_button:
                form_type: Symfony\Component\Form\Extension\Core\Type\SubmitType,
                label: sidus.datagrid.submit.label
                attr:
                    class: btn-primary

            # Optional: Customize the reset button of the filters, this represents the default values
            reset_button:
                form_type: Sidus\DataGridBundle\Form\Type\LinkType
                label: sidus.datagrid.reset.label
                uri: '?' # Default to "action" option of the form if it's defined

            # Datagrid columns definitions
            columns:
                <column_code>:
                    # Use a simple twig template to render the column
                    template: ~ # Use {{ column.render(result) }} to render the result inside the template
                    # /!\ This option has nothing to do with the 'template' option at the datagrid level

                    # This can override the actual property the column renderer will try to resolve
                    property_path: ~ # Default to the column code

                    # Define a sort column different from the column code
                    sort_column: ~

                    # This option allows you to override the value renderer at the column level
                    value_renderer: ~ # Default to the datagrid column_value_renderer

                    # Renderer service id used to render the column label if the label option is not defined
                    label_renderer: ~ # Default to the datagrid column_label_renderer
                    # By default, the system will check if the following translation key exists:
                    # datagrid.<datagrid_code>.<column_code>
                    # If no translation key is found, it will fallback to a humanized version of the column code

                    # If you don't want to use the automatically generated translator keys:
                    label: ~ # This value will still pass through the translator

                    # All these formatting options are only available when using the default DefaultColumnValueRenderer
                    formatting_options:
                        # Date and time options are only used if value is a DateTimeInterface object
                        # Uses the \DateTime::format function when specified
                        date_format: ~ # Not used by default
                        # Default to IntlDateFormatter::format method with better locale support
                        date_type: 2 # IntlDateFormatter::MEDIUM
                        time_type: 3 # IntlDateFormatter::SHORT
                        # If \DateTime object has a time component equals to 00:00
                        # time_type: -1 # IntlDateFormatter::NONE

                        # Decimals related options are only available if value is a float
                        # Warning: integers will be rendered by casting them to string
                        # By default floats will be rendered using the NumberFormatter::format method
                        # using the current locale and the following format:
                        number_format: 1 # NumberFormatter::DECIMAL
                        # If you want to use the number_format function instead, you can set any of these options
                        decimals: ~ # Default to 2
                        dec_point: ~ # Default to .
                        thousands_sep: ~ # Default to ,

                        # If value is iterable, all array items will be rendered recursively using these options:
                        array_glue: ', ' # Implode the array using this character
                        key_value_separator: ': ' # Only used if keys are not numeric

                        # Boolean values will be rendered by default using these translation keys
                        bool_true: sidus.datagrid.boolean.yes
                        bool_false: sidus.datagrid.boolean.no
                        bool_use_translator: true # Set to false if you want to use your values without the translator

                        # Danger zone:
                        # You can override the column option if you want to render a different column
                        # (not recommended, use property_path instead)
                        column: ~ # Will be filled automatically at runtime
                        # This key will be available in your custom renderer, but do not try to set it in configuration
                        object: ~ # Will be filled automatically at runtime, do not alter
````
